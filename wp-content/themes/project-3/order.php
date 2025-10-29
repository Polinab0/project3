<?php
/*
Template Name: Order Panel
Template Post Type: page
*/
get_header();


if (!function_exists('gf_img_url')) {
  function gf_img_url($img){
    if(!$img) return '';
    if(is_array($img) && !empty($img['url'])) return esc_url($img['url']);
    if(is_numeric($img)) return esc_url(wp_get_attachment_image_url((int)$img, 'full'));
    if(is_string($img)) return esc_url($img);
    return '';
  }
}
function op_tab_icon_html($img){
  $src = gf_img_url($img);
  if(!$src) return '';
  $alt = '';
  if(is_array($img)){
    if(!empty($img['alt']))   $alt = $img['alt'];
    elseif(!empty($img['title'])) $alt = $img['title'];
  }
  return '<img class="op-tab__img" src="'.esc_url($src).'" alt="'.esc_attr($alt).'">';
}
function op_to_url_simple($href){
  if (!$href) return '';
  if (is_array($href) && !empty($href['url'])) return $href['url']; // ACF Page Link как массив
  return is_string($href) ? $href : '';
}
function op_row_simple($label, $href){
  $url = op_to_url_simple($href);
  if (!$url) return;
  echo '<a class="op-row" href="'.esc_url($url).'"><span class="op-row__label">'.esc_html($label).'</span></a>';
}


$enabled = get_field('order_enable');


$bg_img   = get_field('order_bg_image');     $bg_url = gf_img_url($bg_img);
$store    = get_field('order_store_name');
$address  = get_field('order_store_address');


$lbl_here = get_field('order_here_label')     ?: 'Here';
$here_hr  = get_field('order_here_hours');
$ico_here = get_field('order_here_icon');    

$lbl_pick = get_field('order_pickup_label')   ?: 'Pick up';
$pick_hr  = get_field('order_pickup_hours');
$ico_pick = get_field('order_pickup_icon');  

$lbl_del  = get_field('order_delivery_label') ?: 'Delivery';
$del_hr   = get_field('order_delivery_hours');
$ico_del  = get_field('order_delivery_icon'); 


$food_label   = get_field('order_food_label')     ?: 'FOOD';
$food_link    = get_field('order_food_link');

$drinks_label = get_field('order_drinks_label')   ?: 'DRINKS';
$drinks_link  = get_field('order_drinks_link');

$dess_label   = get_field('order_desserts_label') ?: 'DESSERTS';
$dess_link    = get_field('order_desserts_link');

$about_label  = get_field('order_about_label')    ?: 'ABOUT US';
$about_link   = get_field('order_about_link');

$merch_label  = get_field('order_merch_label')    ?: 'MERCH';
$merch_link   = get_field('order_merch_link');
?>

<main class="gf-orderpage">

  <?php if ($enabled): ?>


<section class="op-hero" style="<?php if($bg_url) echo '--op-bg:url(' . esc_url($bg_url) . ');'; ?>">
  <div class="op-hero__overlay"></div>


  <div class="op-shell">
    <header class="op-card">
      <div class="op-card__body">
        <h1 class="op-card__title"><?php echo esc_html($store ?: get_bloginfo('name')); ?></h1>
        <?php if ($address): ?><p class="op-card__addr"><?php echo esc_html($address); ?></p><?php endif; ?>
      </div>
    </header>

    <nav class="op-tabs" aria-label="Service modes">
      <button class="op-tab is-active" type="button">
        <?php echo op_tab_icon_html($ico_here); ?>
        <span class="op-tab__label"><?php echo esc_html($lbl_here); ?></span>
        <?php if ($here_hr): ?><small class="op-tab__time"><?php echo esc_html($here_hr); ?></small><?php endif; ?>
      </button>
      <button class="op-tab" type="button">
        <?php echo op_tab_icon_html($ico_pick); ?>
        <span class="op-tab__label"><?php echo esc_html($lbl_pick); ?></span>
        <?php if ($pick_hr): ?><small class="op-tab__time"><?php echo esc_html($pick_hr); ?></small><?php endif; ?>
      </button>
      <button class="op-tab" type="button">
        <?php echo op_tab_icon_html($ico_del); ?>
        <span class="op-tab__label"><?php echo esc_html($lbl_del); ?></span>
        <?php if ($del_hr): ?><small class="op-tab__time"><?php echo esc_html($del_hr); ?></small><?php endif; ?>
      </button>
    </nav>
  </div>
</section>


<section class="op-links">
  <div class="op-shell">
    <?php
      op_row_simple($food_label,   $food_link);
      op_row_simple($drinks_label, $drinks_link);
      op_row_simple($dess_label,   $dess_link);
      op_row_simple($about_label,  $about_link);
      op_row_simple($merch_label,  $merch_link);
    ?>
  </div>
</section>


  <?php else: ?>
    <div class="container" style="padding:2rem 0;">
      <p>Order panel is disabled in ACF.</p>
    </div>
  <?php endif; ?>

</main>

<?php get_footer(); ?>