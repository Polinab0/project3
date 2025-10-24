<?php
/*
Template Name: Halloween – Basic (ACF same fields)
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



$title = get_field('hallow_page_title');
$subtitle = get_field('hallow_subtitle');
$hero_img = get_field('hallow_hero_image');
$hero_alt = get_field('hallow_hero_alt');
$hero_url = gf_img_url($hero_img);


$body = get_field('hallow_body');
$menu_text = get_field('hallow_menu_text');


$dates = get_field('hallow_event_dates');
$time = get_field('hallow_event_time');
$location = get_field('hallow_location');


$cta_label = get_field('hallow_cta_label');
$cta_link = get_field('hallow_cta_link');


if (!$hero_url) {
$hero_url = get_template_directory_uri() . '/assets/img/halloween-fallback.jpg';
}
?>

<main class="gf-hallow">

<section class="gf-hallow__hero" style="<?php if($hero_url) echo '--hero:url(' . esc_url($hero_url) . ');'; ?>">
<div class="gf-hallow__overlay"></div>

<div class="gf-hallow__inner container">
<h1 class="gf-hallow__title">
<?php echo $title ? esc_html($title) : 'Halloween at Grano Flame'; ?>
</h1>

<?php if (!empty($subtitle)) : ?>
<p class="gf-hallow__subtitle"><?php echo esc_html($subtitle); ?></p>
<?php endif; ?>

<?php if (!empty($cta_label)) : ?>
<a class="gf-btn gf-btn--pumpkin" <?php if($cta_link){ ?>href="<?php echo esc_url($cta_link); ?>"<?php } ?>>
<?php echo esc_html($cta_label); ?>
</a>
<?php endif; ?>
</div>

<?php if (!empty($hero_alt)) : ?>
<span class="screen-reader-text"><?php echo esc_html($hero_alt); ?></span>
<?php endif; ?>
</section>


<section class="gf-hallow__content">
<div class="container">

<?php

if (!empty($body)) {
echo wp_kses_post($body);
}


$menu_items = [];
if (!empty($menu_text)) {

$raw = wp_kses( $menu_text, [] );


$lines = explode("\n", $raw);

foreach ($lines as $line) {
$line = trim($line);
if ($line !== '') {
$menu_items[] = $line;
}
}
}
?>

<div class="gf-row-two">
<?php if (!empty($menu_items)) : ?>
<section class="gf-box gf-box--menu">
<h2>Menu Highlights</h2>
<ul class="gf-menu">
<?php foreach ($menu_items as $mi) : ?>
<li class="gf-menu__item"><span><?php echo esc_html($mi); ?></span></li>
<?php endforeach; ?>
</ul>
</section>
<?php endif; ?>

<?php if ($location || $dates || $time) : ?>
<aside class="gf-box gf-box--event">
<h2>Event Details</h2>
<?php if ($location): ?><div class="gf-event__row"><strong>Location:</strong> <?php echo esc_html($location); ?></div><?php endif; ?>
<?php if ($dates): ?><div class="gf-event__row"><strong>Dates:</strong> <?php echo esc_html($dates); ?></div><?php endif; ?>
<?php if ($time): ?><div class="gf-event__row"><strong>Time:</strong> <?php echo esc_html($time); ?></div><?php endif; ?>
</aside>
<?php endif; ?>
</div>

</section>

</main>

<?php get_footer(); ?>