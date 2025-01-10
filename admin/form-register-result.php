<?php
include 'form-result/table.php';
include 'form-result/button.php';

Class FormRegisterResultAdmin {

    static function page(\SkillDo\Http\Request $request, $params): void {

        $formKey = $request->input('form-key');

        if(empty($formKey)) {
            echo Admin::alert('error', 'Không tìm thấy loại form để hiển thị');
            return;
        }

        $form = \FormRegister\Model\Form::where('key', $formKey)->select('name')->first();

        if(empty($form)) {
            echo Admin::alert('error', 'Form không tồn tại');
            return;
        }

        $table = new \FormRegister\Table\AdminResult();

        Admin::view('components/page-default/page-index', [
            'module'    => 'form_register_result',
            'name'      => $form->name,
            'table'     => $table,
        ]);
    }
    static function breadcrumb($breadcrumb, $pageIndex, \SkillDo\Http\Request $request): array
    {
        if($pageIndex == 'plugins_page') {

            $page = Url::segment(3);

            if($page == 'form_register_result') {

                $breadcrumb['form_register_result'] = [
                    'active' => true,
                    'label' => trans('Form đăng ký')
                ];
            }
        }

        return $breadcrumb;
    }
    static function count(): void {
        if(Template::isPage('plugins_page')) {

            $plugin = Url::segment(3);

            if($plugin == 'form_register_result') {

                $formKey = request()->input('form-key');

                if(!empty($formKey)) {

                    $cacheId = 'generate_form_count_'.$formKey;

                    $count =  \SkillDo\Cache::get($cacheId);

                    if(!is_numeric($count) || $count > 0) {

                        \FormRegister\Model\FormResult::where('status', 1)->update(['status' => 0]);

                        \SkillDo\Cache::delete($cacheId);
                    }
                }
            }
        }
    }
    static function modalExport(): void
    {
        if(Template::isPage('plugins_page')) {

            $plugin = Url::segment(3);

            if($plugin == 'form_register_result') {

                $formKey = request()->input('form-key');

                if(!empty($formKey)) {

                    Plugin::view('generate-form-register', 'views/admin/export/modal');
                }
            }
        }
    }
}

AdminMenu::add('form_register_result','Đăng ký email', 'plugins/form_register_result', [
    'callback' => 'FormRegisterResultAdmin::page',
    'hidden' => true,
]);

add_filter('admin_breadcrumb', 'FormRegisterResultAdmin::breadcrumb', 50, 3);
add_action('admin_footer', 'FormRegisterResultAdmin::count');
add_action('admin_footer', 'FormRegisterResultAdmin::modalExport');