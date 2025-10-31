<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?> 
</head>
<body <?php body_class(); ?>>

<nav class="site-nav">
  <?php
 
  $settings_page = get_page_by_path('header-settings', OBJECT, 'page');
  $settings_id   = $settings_page ? $settings_page->ID : (int) get_option('page_on_front');


  $has_acf     = function_exists('get_field');
  $logo        = $has_acf ? get_field('logo_image', $settings_id) : null;
  $logo_link   = $has_acf ? (get_field('logo_link', $settings_id) ?: home_url('/')) : home_url('/');
  $show_search = $has_acf ? (bool) get_field('show_search', $settings_id) : true;
  $show_lang   = $has_acf ? (bool) get_field('show_lang_button', $settings_id) : true;
  $lang_label  = $has_acf ? (get_field('lang_label', $settings_id) ?: 'EN') : 'EN';
  ?>
  <div class="site-nav__inner">


    <a href="<?php echo esc_url($logo_link); ?>" class="site-nav__logo" aria-label="Home">
      <?php
      if ($logo && !empty($logo['ID'])) {
        echo wp_get_attachment_image($logo['ID'], 'medium');
      } else {
        echo '<span>Logo</span>';
      }
      ?>
    </a>

  
    <div class="site-nav__middle">
      <?php
   
        $order_enable = $has_acf ? (bool) get_field('order_button_enable', $settings_id) : true;
        $order_label  = $has_acf ? (get_field('order_button_label', $settings_id) ?: 'Order') : 'Order';
        $order_raw    = $has_acf ? get_field('order_button_link', $settings_id) : '';
        $order_url    = '';
        $order_target = '';

      
        if (is_array($order_raw) && !empty($order_raw['url'])) {
          $order_url    = $order_raw['url'];
          $order_target = $order_raw['target'] ?? '';
        } elseif ($order_raw instanceof WP_Post) {
          $order_url = get_permalink($order_raw);
        } elseif (is_numeric($order_raw)) {
          $order_url = get_permalink((int) $order_raw);
        } elseif (is_string($order_raw) && $order_raw !== '') {
          $order_url = $order_raw;
        }
      ?>

      <?php if ($order_enable && $order_url): ?>
        <a href="<?php echo esc_url($order_url); ?>"
           class="pill pill--order site-nav__order"
           <?php echo $order_target ? 'target="'.esc_attr($order_target).'" rel="noopener"' : ''; ?>>
          <?php echo esc_html($order_label); ?>
        </a>
      <?php endif; ?>
    </div>

   
    <div class="site-nav__actions">

      <?php if ($show_search): ?>
        <div class="search-container">
          <button id="search-button" class="search-icon" type="button" aria-label="Search">
            <?php
              $page = get_page_by_title('Page1');
              $icon = ($page && $has_acf) ? get_field('search_icon', $page->ID) : null;
              if (!empty($icon['url'])) {
                echo '<img src="'.esc_url($icon['url']).'" alt="Search" width="20" height="20">';
              } else {
            
                echo '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
              }
            ?>
          </button>

          <form id="search-form" class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" name="s" class="search-field" placeholder="Search">
            <button type="submit" class="search-submit">OK</button>
          </form>
        </div>
      <?php endif; ?>

      <?php if ($show_lang): ?>
        <div class="pill pill--text">
          <?php echo esc_html($lang_label); ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</nav>
