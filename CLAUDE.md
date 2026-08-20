# CLAUDE.md — Plugin generate-form-register

File này giúp agent hiểu ngay cấu trúc plugin mà không cần scan lại source. Đọc TRƯỚC khi sửa bất kỳ
file nào trong plugin.

Nơi **mọi form của site** đổ dữ liệu về. Không tự dựng bảng lưu form mới — kiểm plugin này trước.

- Class chính `GenerateFormRegister` (`index.php`), namespace `FormRegister\*`, alias `FormRegister`.
- Version 5.0.3.

## Bảng dữ liệu

| Bảng | Vai trò |
|---|---|
| `generate_form_register` | định nghĩa form. Cột `key` phải TRÙNG `form_key` mà form ngoài trang gửi lên |
| `form_register_result` | bài gửi. Chỉ có sẵn cột `name` · `email` · `phone` · `message` · `status` · `form_key` |
| `form_register_result_metadata` | mọi ô nhập PHỤ (ngày, giờ, dịch vụ…) |

⚠ Cột `generate_form_register.field` là **PHP `serialize()`**, không phải JSON, và **bắt buộc** có
cả hai khoá `default` lẫn `metadata`:

```php
serialize([
    'default' => [
        'name'    => ['use'=>'1','field'=>'name','label'=>'Họ và tên','required'=>'1','limit'=>'0'],
        'email'   => ['use'=>'1','field'=>'email','label'=>'Email','required'=>'1','isEmail'=>'1'],
        'phone'   => ['use'=>'1','field'=>'phone','label'=>'SĐT','required'=>'1','isPhone'=>'0'],
        'message' => ['use'=>'0','field'=>'note','label'=>'Ghi chú','required'=>'0'],
    ],
    'metadata' => [
        'date' => ['use'=>'1','name'=>'date','field'=>'date','label'=>'Ngày','required'=>'1'],
    ],
]);
```

`name` = khoá lưu vào bảng metadata · `field` = **name của thẻ input** ngoài trang. Lệch nhau là giá
trị bị bỏ đi lặng lẽ. Khoá `message` ánh xạ sang cột `message` nhưng input mặc định tên là `note`.

## Cách form ngoài trang nối vào

`assets/js/form-register-script.js` (nạp qua `theme_custom_assets`) bắt `submit` trên
**`.email-register-form`** và **`.form-information`**, tự gói dữ liệu + CSRF rồi bắn tới
`FormRegister\Ajax\Web\FormRegisterAjax::register`. Form chỉ cần:

```html
<form class="form-information">
    <input type="hidden" name="form_key" value="<key>">
    ...
</form>
```

Đừng tự viết ajax. `FormElement` của Page Builder đã render đúng class này sẵn.

## ⛔ Bước dễ quên nhất: `FormRegisterHelper::build()`

Plugin **sinh code** cho từng form đang bật (`is_live = 1`):

- `app/Builds/<StudlyKey>Build.php` — mục menu trong Marketing + định nghĩa cột bảng kết quả
- `bootstrap/build.php` — các `add_action` / `add_filter` trỏ tới những class đó

Script seed chỉ `INSERT` vào `generate_form_register` thì **bài gửi vẫn lưu** nhưng **không có màn
hình nào trong admin để xem** — nhìn y hệt "form không lưu được". Luôn kết thúc script seed bằng:

```php
\FormRegister\Supports\FormRegisterHelper::build();
```

Rồi `ls app/Builds/` xác nhận đúng các form của dự án hiện tại. `build()` xoá sạch thư mục trước khi
sinh lại, nên nó cũng là cách dọn build sót của dự án cũ.

## Bảng kết quả trong admin — ba ràng buộc dễ làm hỏng khi sửa

`app/Modules/Admin/FormRegisterResult/FormRegisterResultTable.php`
1. `actionButton()` **phải** truyền `'module' => $this->module` cho `Admin::btnDelete`. Thiếu nó thì
   bấm xoá chỉ nhận **"Bạn vui lòng cung cấp module cần xóa"** — `ActionAjax::delete()` chặn ngay ở
   đầu bằng `if (empty($module))`, và câu báo lỗi không nhắc tới bảng nào nên rất khó chẩn.
   (Sửa ở 5.0.3, trước đó xoá dòng kết quả không bao giờ chạy được.)
2. `bulkAction()` khai xoá hàng loạt cho cột `cb`. Dùng `'data-ajax' => 'Admin\Ajax\ActionAjax::delete'`
   — **đừng** chép `Ajax_Admin_Action::delete` từ `views/admin/app/Modules/{Page,Post,Tag}/Table.php`:
   đó là tên thời v6, `views/admin/bootstrap/ajax.php` không đăng ký nữa và `Ajax::getAction()` so
   khớp bằng `==` tuyệt đối, nên dispatcher trả `{"status":"error","message":"ajax action not found"}`
   còn JS bảng thì không hiện thông báo ⇒ nút chết lặng. (Ba bảng core đã được sửa ở core; site nào
   chưa cập nhật thì vẫn dính — cứ khai tên đúng ở đây là an toàn cho mọi phiên bản.)

`app/Services/FormRegisterAdminService.php` + `bootstrap/config.php`
3. `deleteMetadata()` gắn vào `ajax_delete_form_register_result_before_success`. `ActionAjax::delete()`
   chỉ gọi `$model::whereKey($ids)->delete()`, không đụng tới `form_register_result_metadata`; form có
   ô phụ (ngày/giờ/dịch vụ) vì thế để lại dòng mồ côi và khi id được cấp lại thì dữ liệu cũ hiện lên
   bản ghi mới.
   ⚠ `Metadata::delete()` chặn bằng `is_numeric($object_id)`: truyền **mảng** là lặng lẽ trả `false`,
   phải lặp từng id.

## Bẫy khác

- Không có dòng `generate_form_register` khớp `form_key` ⇒ handler báo lỗi rõ ("Không tìm thấy form
  đăng ký có khoá ..."). Nếu người dùng bảo "bấm gửi không thấy gì", kiểm khoá này TRƯỚC.
- `is_live = 0` ⇒ form từ chối nhận, và `build()` cũng bỏ qua nên mục menu biến mất.
- `app/Builds/` là code SINH RA, đừng sửa tay — chạy lại `build()` là mất.
