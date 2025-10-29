<?php


function demo_load_stylesheet() {

    wp_enqueue_style("bootstrap", "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css");
   
    wp_enqueue_style("main", get_template_directory_uri() . "/style.css");

   
    if (is_front_page()) {
        wp_enqueue_style("front", get_template_directory_uri() . "/front.css", array("main"));
    }



}
add_action("wp_enqueue_scripts", "demo_load_stylesheet");




add_action('wp_enqueue_scripts', function () {

  wp_enqueue_style(
    'gf-titan-one',
    'https://fonts.googleapis.com/css2?family=Titan+One&display=swap',
    [],
    null
  );
});

add_theme_support('post-thumbnails');





function my_get_register_url() {
    $reg = function_exists('wp_registration_url') ? wp_registration_url() : '';
    if (!$reg) { $reg = wp_login_url() . '?action=register'; }
    return $reg;
}

add_action('admin_post_t_submit', function () {
    if (!is_user_logged_in()) { do_action('admin_post_nopriv_t_submit'); return; }
    if (!isset($_POST['t_nonce']) || !wp_verify_nonce($_POST['t_nonce'],'t_submit_action')) {
        wp_die('Security check failed.', 'Security error', ['response'=>403]);
    }
    $title = isset($_POST['t_title']) ? sanitize_text_field($_POST['t_title']) : '';
    $text_raw = isset($_POST['t_text']) ? (string) $_POST['t_text'] : '';
    $text = wp_kses_post($text_raw);
    $post_id = wp_insert_post([
        'post_type'=>'testimonial',
        'post_title'=>$title,
        'post_content'=>$text,
        'post_status'=>'pending',
        'post_author'=>get_current_user_id(),
    ]);
    if (is_wp_error($post_id)) { wp_die('Could not save your testimonial.', 'Error', ['response'=>500]); }
    $back = wp_get_referer() ? wp_get_referer() : home_url('/');
    wp_safe_redirect(add_query_arg('t_ok','1',$back));
    exit;
});

add_action('admin_post_nopriv_t_submit', function () {
    $login_url = esc_url(wp_login_url());
    $register_url = esc_url(my_get_register_url());
    wp_die('Only logged-in users can submit testimonials.<br><br><a href="'.$login_url.'">Log in</a> or <a href="'.$register_url.'">register</a>.','Access denied',['response'=>403]);
});





// === Создаём стандартные термины для таксономии menu_section, если их нет
add_action('init', function () {
  // убедись, что таксономия уже существует (её создал ACF)
  if (taxonomy_exists('menu_section')) {
    $terms = [
      'salty-food' => 'Salty food',
      'drinks'     => 'Drinks',
      'desserts'   => 'Desserts',
      'tableware'  => 'Our tableware',
    ];
    foreach ($terms as $slug => $name) {
      if (!term_exists($slug, 'menu_section')) {
        wp_insert_term($name, 'menu_section', ['slug' => $slug]);
      }
    }
  }
});

// === На всякий случай: после активации темы — обновить правила ссылок
add_action('after_switch_theme', function () {
  flush_rewrite_rules();
});



add_action('wp_enqueue_scripts', function () {
  if (is_page_template('menu.php')) {
    wp_enqueue_style('menu-css',
      get_stylesheet_directory_uri() . '/css/menu.css',
      [],
      '1.0'
    );
  }
});


// Підключення стилів/скриптів тільки на page-menu.php
add_action('wp_enqueue_scripts', function () {
  if ( is_page_template('page-menu.php') ) {

    wp_enqueue_script(
      'menu-order',
      get_stylesheet_directory_uri() . '/assets/menu-order.js',
      [],
      '1.0',
      true
    );
  }
});




// === AJAX: приём заказа без плагинов ===
// (CPT 'orders' ты уже создала через CPT UI)

add_action('wp_ajax_nopriv_place_order', 'my_place_order_handler');
add_action('wp_ajax_place_order',        'my_place_order_handler');

function my_place_order_handler(){
  // защита
  if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'place_order')) {
    wp_send_json_error(['message' => 'Security check failed'], 403);
  }
  if (!empty($_POST['hp'])) { // honeypot
    wp_send_json_error(['message' => 'Bot detected'], 400);
  }

  // данные формы
  $full_name   = sanitize_text_field($_POST['full_name'] ?? '');
  $phone       = sanitize_text_field($_POST['phone'] ?? '');
  $email       = sanitize_email($_POST['email'] ?? '');
  $address     = sanitize_text_field($_POST['address'] ?? '');
  $order_total = sanitize_text_field($_POST['order_total'] ?? '€0.00');
  $order_data  = wp_kses_post($_POST['order_data'] ?? '');  // список позиций строками
  $order_json  = wp_unslash($_POST['order_json'] ?? '[]');  // JSON-строка корзины

  if (!$full_name || !$phone || !$email || !$address) {
    wp_send_json_error(['message' => 'Please fill all required fields'], 422);
  }

  // создаём запись типа 'orders'
  $post_id = wp_insert_post([
    'post_type'   => 'orders',
    'post_status' => 'publish',
    'post_title'  => 'Order — '.$full_name.' — '.$order_total.' — '.current_time('mysql'),
  ], true);

  if (is_wp_error($post_id)) {
    wp_send_json_error(['message' => 'Failed to save order'], 500);
  }

  // сохраняем мета (имена совпадают с ACF Field Name)
  update_post_meta($post_id, 'full_name',   $full_name);
  update_post_meta($post_id, 'phone',       $phone);
  update_post_meta($post_id, 'email',       $email);
  update_post_meta($post_id, 'address',     $address);
  update_post_meta($post_id, 'order_total', $order_total);
  update_post_meta($post_id, 'order_data',  $order_data);
  update_post_meta($post_id, 'order_json',  $order_json);

  // письмо админу (в Local увидишь в MailPit)
  $to       = get_option('admin_email');
  $subject  = 'New Order from '.$full_name;
  $headers  = ['Content-Type: text/plain; charset=UTF-8'];
  $body     = "New order\n\n".
              "Name: $full_name\nPhone: $phone\nEmail: $email\nAddress: $address\n\n".
              "Total: $order_total\n\n".
              "Items:\n$order_data\n\n".
              "View in admin: ".admin_url('post.php?post='.$post_id.'&action=edit');

  wp_mail($to, $subject, $body, $headers);

  wp_send_json_success(['message' => 'Order saved', 'order_id' => $post_id]);
}
