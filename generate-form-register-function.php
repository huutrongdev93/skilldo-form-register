<?php
Class Form_Register {
    static string $table = 'generate_form_register';
    static public function handleParams($args) {
        $query = ($args instanceof Qr) ? clone $args : Qr::set();
        if(is_array($args)) {
            $query = Qr::convert($args,);
            if(!$query) return $query;
        }
        if(is_numeric($args)) $query = Qr::set(self::$table.'.id', $args);
        return $query;
    }
    public static function get($args = []) {
        $args = self::handleParams($args);
        if(!$args instanceof Qr) return new Illuminate\Support\Collection();
        return apply_filters('get_generate_form_register', model(self::$table)->get($args), $args);
    }
    public static function getBy($field, $value) {
        return apply_filters('get_generate_form_register_by', static::get(Qr::set($field, $value)), $field, $value );
    }
    public static function gets($args = []) {
        $args = self::handleParams($args);
        if(!$args instanceof Qr) return new Illuminate\Support\Collection();
        return apply_filters('gets_generate_form_register', model(self::$table)->gets($args), $args);
    }
    public static function getsBy($field, $value, $params = []) {
        return apply_filters('gets_generate_form_register_by', static::gets(Qr::set($field, $value)), $field, $value );
    }
    public static function count($args = []) {
        $args = self::handleParams($args);
        if(!$args instanceof Qr) return new Illuminate\Support\Collection();
        return apply_filters('count_generate_form_register', model(self::$table)->count($args), $args);
    }
    public static function insert($generate_form_register = []) {
        if (!empty($generate_form_register['id'])) {
            $id             = (int) $generate_form_register['id'];
            $update         = true;
            $old_generate_form_register = static::get($id);
            if (!$old_generate_form_register) return new SKD_Error('invalid_generate_form_register_id', __( 'ID trang không chính xác.' ));
            $generate_form_register['name'] = (!empty($generate_form_register['name'])) ? $generate_form_register['name'] : $old_generate_form_register->name;
            $generate_form_register['key'] = (!empty($generate_form_register['key'])) ? $generate_form_register['key'] : $old_generate_form_register->key;
            $generate_form_register['field'] = (!empty($generate_form_register['field'])) ? $generate_form_register['field'] : $old_generate_form_register->field;
            $generate_form_register['taxonomy'] = (!empty($generate_form_register['taxonomy'])) ? $generate_form_register['taxonomy'] : $old_generate_form_register->taxonomy;
            $generate_form_register['taxonomy_config'] = (!empty($generate_form_register['taxonomy_config'])) ? $generate_form_register['taxonomy_config'] : $old_generate_form_register->taxonomy_config;
            $generate_form_register['taxonomy_icon'] = (!empty($generate_form_register['taxonomy_icon'])) ? $generate_form_register['taxonomy_icon'] : $old_generate_form_register->taxonomy_icon;
            $generate_form_register['is_live'] = (isset($generate_form_register['is_live'])) ? $generate_form_register['is_live'] : $old_generate_form_register->is_live;
            $generate_form_register['is_redirect'] = (isset($generate_form_register['is_redirect'])) ? $generate_form_register['is_redirect'] : $old_generate_form_register->is_redirect;
            $generate_form_register['send_email'] = (isset($generate_form_register['send_email'])) ? $generate_form_register['send_email'] : $old_generate_form_register->send_email;
            $generate_form_register['email_template'] = (!empty($generate_form_register['email_template'])) ? $generate_form_register['email_template'] : $old_generate_form_register->email_template;
            $generate_form_register['url_redirect'] = (isset($generate_form_register['url_redirect'])) ? $generate_form_register['url_redirect'] : $old_generate_form_register->url_redirect;
        }
        else {
            $update = false;
        }

        $name               = (!empty($generate_form_register['name'])) ?       Str::clear($generate_form_register['name']) : '';
        $key                = (!empty($generate_form_register['key'])) ?        Str::clear($generate_form_register['key']) : '';
        $field              = (!empty($generate_form_register['field'])) ?      Str::clear($generate_form_register['field']) : '';
        $url_redirect       = (isset($generate_form_register['url_redirect'])) ? Str::clear($generate_form_register['url_redirect']) : '';
        $taxonomy           = (!empty($generate_form_register['taxonomy'])) ?   Str::clear($generate_form_register['taxonomy']) : '';
        $taxonomy_config    = (!empty($generate_form_register['taxonomy_config'])) ? Str::clear($generate_form_register['taxonomy_config']) : '';
        $email_template     = (!empty($generate_form_register['email_template'])) ? $generate_form_register['email_template'] : '';
        $taxonomy_icon      = (!empty($generate_form_register['taxonomy_icon'])) ? FileHandler::handlingUrl($generate_form_register['taxonomy_icon']) : '';
        $is_live            = (isset($generate_form_register['is_live'])) ?     (int)$generate_form_register['is_live'] : 0;
        $is_redirect        = (isset($generate_form_register['is_redirect'])) ?     (int)$generate_form_register['is_redirect'] : 0;
        $send_email         = (isset($generate_form_register['send_email'])) ?  (int)$generate_form_register['send_email'] : 0;
        $data = compact( 'name', 'key','field','taxonomy', 'taxonomy_icon','taxonomy_config','is_live', 'send_email', 'email_template', 'is_redirect', 'url_redirect' );
        $data = apply_filters( 'pre_insert_generate_form_register_data', $data, $generate_form_register, $update ? (int) $id : null );
        $model 	= model(self::$table);
        if ($update) {
            $model->update($data, Qr::set($id));
            $generate_form_register_id = (int) $id;
        } else {
            $generate_form_register_id = $model->add($data);
        }
        return apply_filters( 'after_insert_generate_form_register', $generate_form_register_id, $generate_form_register, $data, $update ? (int) $id : null  );
    }
    public static function delete($generate_form_registerID = 0) {
        $generate_form_registerID = (int)$generate_form_registerID;
        if( $generate_form_registerID == 0 ) return false;
        $count = static::count( $generate_form_registerID );
        if($count == 1) {
            $model 	= model(self::$table);
            do_action('delete_generate_form_register', $generate_form_registerID );
            if($model->delete(Qr::set($generate_form_registerID))) {
                do_action('delete_generate_form_register_success', $generate_form_registerID );
                return [$generate_form_registerID];
            }
        }
        return false;
    }
    public static function taxonomyConfig($taxonomyConfig = '') {
        if(empty($taxonomyConfig)) return [];
        $taxonomyConfig = explode("\n", $taxonomyConfig);
        foreach ($taxonomyConfig as $value) eval('$'.$value.';');
        if(!isset($name)) $name = '';
        if(!isset($icon)) $icon = '';
        return compact('name','icon');
    }
    public static function config($field = '', $type = '') {

        if(empty($field)) return [];

        $config = [];

        $field = explode("\n", $field);

        foreach ($field as $key => $value) {

            $value = explode('|', $value);

            if(count($value) >= 5) {

                if($type == 'data' && $value[3] != 'data') continue;

                if($type == 'metadata' && $value[3] != 'metadata') continue;

                $config[$value[0]] = [
                    'field' => $value[1],
                    'label' => $value[2],
                    'type'  => $value[3],
                    'table_show' => $value[4],
                ];

                if(isset($value[5])) {
                    $config[$value[0]]['rule'] = explode(',', $value[5]);
                }
            }
        }

        return $config;
    }
    public static function generateCodeTaxonomy($form = []) {

        $taxonomy_key = $form->taxonomy;

        $config = array_merge(['name' => '', 'icon' => '<img src="'.Path::plugin('generate-form-register').'/icon-email.png" />'],Form_Register::taxonomyConfig($form->taxonomy_config));

        if(empty($config['icon'])) $config['icon'] = '<img src="'.Path::plugin('generate-form-register').'/icon-email.png" />';

        $fields = Form_Register::config($form->field);

        $cache_id = 'generate_form_count_'.$taxonomy_key;

        $count =  CacheHandler::get($cache_id);

        if(!is_numeric($count)) {
            $count = Posts::count(Qr::set('post_type', $taxonomy_key)->where('status', 1));
            CacheHandler::save($cache_id, $count);
        }

        ob_start();?>
        AdminMenu::add('<?php echo $taxonomy_key;?>','<?php echo $config['name'];?>','post?post_type=<?php echo $taxonomy_key;?>',['icon' => '<?php echo $config['icon'];?>', 'position' => 'post', 'count' => '<?php echo $count;?>']);
        Taxonomy::addPost('<?php echo $taxonomy_key;?>',
            array(
                'labels' => array(
                    'name'          => '<?php echo $config['name'];?>',
                    'singular_name' => '<?php echo $config['name'];?>',
                ),
                'public' => false,
                'show_admin_column'  => false,
                'capabilities' => array(
                    'view'      => 'view_email_register',
                    'edit'      => 'edit__<?php echo $taxonomy_key;?>',
                    'delete'    => 'delete_email_register',
                ),
                'supports' => ['group' => ['info']]
            )
        );
        function generate_form_register_<?php echo $taxonomy_key;?>_single_row( $columns, $item ) {
            return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
        }
        add_filter('single_row_post_<?php echo $taxonomy_key;?>', 'generate_form_register_<?php echo $taxonomy_key;?>_single_row', 10, 2);

        function generate_form_register_<?php echo $taxonomy_key;?>_column( $columns ) {
            $columnsnew['cb']   	= 'cb';
            <?php foreach ($fields as $key => $input) { if($input['table_show'] == 'true') echo '$columnsnew["'.$input['field'].'"] = "'.$input['label'].'";';} ?>
            $columnsnew['created'] 	= 'Ngày đăng ký';
            $columnsnew['action'] 	= 'Hành động';
            return $columnsnew;
        }
        add_filter('manage_post_<?php echo $taxonomy_key;?>_columns', 'generate_form_register_<?php echo $taxonomy_key;?>_column', 10);

        function generate_form_register_data_<?php echo $taxonomy_key;?>_column( $column_name, $item ) {
            switch ( $column_name ) {
                <?php foreach ($fields as $key => $input) {
                    if($input['type'] == 'data' && $input['table_show'] == 'true') {
                        echo 'case "'.$input['field'].'":echo ($item->'.$input['field'].') ? apply_filters(\'generate_admin_table_'.$taxonomy_key.'_data\', $item->'.$input['field'].', \''.$input['field'].'\') : ""; break;';
                    }
                    if($input['type'] == 'metadata' && $input['table_show'] == 'true') {
                        echo 'case "'.$input['field'].'":$metadata = Posts::getMeta($item->id, \''.$input['field'].'\', true); echo apply_filters(\'generate_admin_table_'.$taxonomy_key.'_data\', $metadata, \''.$input['field'].'\'); break;';
                    }
                } ?>
            }
        }
        add_action('manage_post_<?php echo $taxonomy_key;?>_custom_column', 'generate_form_register_data_<?php echo $taxonomy_key;?>_column',10,2);
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}