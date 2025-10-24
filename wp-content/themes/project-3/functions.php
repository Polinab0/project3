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
