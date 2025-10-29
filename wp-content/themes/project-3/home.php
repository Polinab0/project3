<?php get_header() ?>
<main class="site-main">
<div class="blog">
  <div class="blog__header"></div>
  

  <?php
  $blog_page_id = get_option('page_for_posts');

 
  $labels = array();
  if (function_exists('get_field')) {
    $labels[] = get_field('top_button_0_label', $blog_page_id);
    $labels[] = get_field('top_button_1_label', $blog_page_id);
    $labels[] = get_field('top_button_2_label', $blog_page_id);
    $labels[] = get_field('top_button_3_label', $blog_page_id);
  }

  $buttons = array();
  foreach ($labels as $lbl) {
    if (!$lbl) continue;
    $label  = trim($lbl);
    $anchor = sanitize_title($label);
    $buttons[] = array('label' => $label, 'anchor' => $anchor);
  }
  ?>

  <?php if (!empty($buttons)): ?>
    <nav class="section-nav" aria-label="Sections">
      <?php foreach ($buttons as $btn): ?>
        <a class="section-nav__btn" href="#<?php echo esc_attr($btn['anchor']); ?>">
          <?php echo esc_html($btn['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="blog__divider" aria-hidden="true"></div>
  <?php endif; ?>


  <?php

  $sec0_title = function_exists('get_field') ? (get_field('section0_title', $blog_page_id) ?: 'Our Tableware') : 'Our Tableware';
  $sec1_title = function_exists('get_field') ? (get_field('section1_title', $blog_page_id) ?: 'Salty food') : 'Salty food';
  $sec2_title = function_exists('get_field') ? (get_field('section2_title', $blog_page_id) ?: 'Drinks')     : 'Drinks';
  $sec3_title = function_exists('get_field') ? (get_field('section3_title', $blog_page_id) ?: 'Desserts')   : 'Desserts';

  $sections = [
    ['our_tableware', $sec0_title, 2],
    ['salty-food', $sec1_title, 12],
    ['drinks',     $sec2_title, 14],
    ['desserts',   $sec3_title, 12],
  ];

  foreach ($sections as $s):
    $cat   = $s[0];
    $title = $s[1];
    $count = (int)$s[2];
    $id    = sanitize_title($title);

    $term = get_category_by_slug($cat);
    if (!$term) {
      $term = get_term_by('name', $title, 'category');
    }
    $cat_id = $term ? (int)$term->term_id : 0;

    $q = new WP_Query([
      'post_type'      => 'post',
      'posts_per_page' => $count,
      'cat'            => $cat_id,
    ]);
  ?>

    <section id="<?php echo esc_attr($id); ?>" class="blog-section">
      <h1 class="blog-section__title"><?php echo esc_html($title); ?></h1>

      <?php if ($q->have_posts()): ?>
        <div class="blog__grid">
          <?php while ($q->have_posts()): $q->the_post(); ?>
            <?php
            $url     = get_the_permalink();
            $ttl     = get_the_title();
            $excerpt = get_the_excerpt();

            $img = ''; $alt = '';
            if ($thumb_id = get_post_thumbnail_id()) {
              $img_large = wp_get_attachment_image_src($thumb_id, 'large');
              $img_full  = wp_get_attachment_image_src($thumb_id, 'full');
              $img = $img_large ? $img_large[0] : ($img_full ? $img_full[0] : '');
              $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
            }
            ?>
            <article class="blog-card">
              <?php if ($img): ?>
                <div class="blog-card__image">
                  <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($alt); ?>">
                </div>
              <?php endif; ?>

              <h2 class="blog-card__title"><?php echo esc_html($ttl); ?></h2>
              <p class="blog-card__excerpt"><?php echo esc_html($excerpt); ?></p>

              <div class="blog-card__footer">
                <span class="blog-card__badge">Blog</span>
                <a href="<?php echo esc_url($url); ?>" class="blog-card__button">Read more</a>
              </div>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      <?php else: ?>
        <p class="blog__empty">No posts in <?php echo esc_html($title); ?>.</p>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>

</div>

</main>
<?php get_footer() ?>
