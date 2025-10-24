<?php get_header(); 
/*
Template Name: Front Page
*/
?>

<?php
$home_id      = get_queried_object_id();
$hero_title   = get_field('hero_title', $home_id);
$hero_desc    = get_field('hero_desc', $home_id);
$hero_phone   = get_field('hero_phone', $home_id);
$hero_crois   = get_field('hero_croissant', $home_id);
$icon_left    = get_field('hero_icon_left', $home_id);
$icon_right   = get_field('hero_icon_right', $home_id);
$hero_time    = get_field('hero_time', $home_id);
$hero_address = get_field('hero_address', $home_id);
?>

<main class="site-main">
  <section class="hero">
    <div class="hero__inner">

      <?php if ($hero_title): ?>
        <h1 class="hero__title"><?php echo esc_html($hero_title); ?></h1>
      <?php endif; ?>

      <div class="hero__grid">
       
        <aside class="hero__side hero__side--left">
          <?php if (!empty($icon_left['ID'])): ?>
            <?php echo wp_get_attachment_image($icon_left['ID'], 'medium', false, ['class'=>'kid']); ?>
          <?php endif; ?>
          <div class="info__text">
            <?php if ($hero_time): ?><div class="info__time"><?php echo esc_html($hero_time); ?></div><?php endif; ?>
            <?php if ($hero_address): ?><div class="info__addr"><?php echo esc_html($hero_address); ?></div><?php endif; ?>
          </div>
        </aside>

      
        <div class="hero__center">
          <div class="phone-wrap">
            <?php if (!empty($hero_phone['ID'])): ?>
              <?php echo wp_get_attachment_image($hero_phone['ID'], 'large', false, ['class'=>'phone']); ?>
            <?php endif; ?>

            <?php if (!empty($hero_crois['ID'])): ?>
              <?php echo wp_get_attachment_image($hero_crois['ID'], 'large', false, ['class'=>'croissant']); ?>
            <?php endif; ?>
          </div>
        </div>

        
        <aside class="hero__side hero__side--right">
          <?php if (!empty($icon_right['ID'])): ?>
            <?php echo wp_get_attachment_image($icon_right['ID'], 'medium', false, ['class'=>'chef']); ?>
          <?php endif; ?>
          <?php if ($hero_desc): ?>
            <p class="hero__desc"><?php echo esc_html($hero_desc); ?></p>
          <?php endif; ?>
        </aside>
      </div>

    </div>
  </section>




<?php

$home_id   = get_queried_object_id();
$g_title   = get_field('green_title', $home_id);
$g_txt1    = get_field('green_text1', $home_id);
$g_txt2    = get_field('green_text2', $home_id);
$g_btn_txt = get_field('green_button_label', $home_id) ?: 'READ MORE';
$g_btn_url = get_field('green_button_link',  $home_id);
?>

<section class="green">
  <div class="green__inner">
    <?php if ($g_title): ?>
      <h2 class="green__title"><?php echo esc_html($g_title); ?></h2>
    <?php endif; ?>

    <?php if ($g_txt1): ?>
      <p class="green__p"><?php echo esc_html($g_txt1); ?></p>
    <?php endif; ?>

    <?php if ($g_txt2): ?>
      <p class="green__p"><?php echo esc_html($g_txt2); ?></p>
    <?php endif; ?>

    <?php if ($g_btn_url): ?>
      <div class="green__btnwrap">
        <a class="btn-pill" href="<?php echo esc_url($g_btn_url); ?>">
          <?php echo esc_html($g_btn_txt); ?>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>


<?php

$home_id  = get_queried_object_id();
$t_title  = get_field('themes_title', $home_id);
$t_p1     = get_field('themes_text1', $home_id);
$t_p2     = get_field('themes_text2', $home_id);


$tiles = get_posts([
  'post_type'      => 'theme_tile',
  'posts_per_page' => 6,
  'orderby'        => 'date', 
  'order'          => 'ASC',
]);
?>

<section class="themes">
  <div class="themes__inner">
    
    <div class="themes__left">
      <h2 class="themes__title"><?php echo esc_html($t_title); ?></h2>
      <p class="themes__p"><?php echo nl2br(esc_html($t_p1)); ?></p>
      <p class="themes__p"><?php echo nl2br(esc_html($t_p2)); ?></p>
    </div>

    
    <div class="themes__right">
      <div class="themes__grid">
        <?php foreach ($tiles as $post): setup_postdata($post); ?>
          <?php
            $id   = $post->ID;
            $img  = get_field('tile_image', $id); 
            $text = get_field('tile_text',  $id); 
          ?>
          <article class="tile">
            <div class="tile__img">
              <?php
                if (!empty($img['ID'])) {
                  echo wp_get_attachment_image($img['ID'], 'large', false, ['loading'=>'lazy','decoding'=>'async']);
                }
              ?>
            </div>
            <p class="tile__title"><?php echo esc_html($text); ?></p>
          </article>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </div>
</section>


<?php
// ===== Latest news  =====
$home_id    = (int) get_option('page_on_front') ?: get_queried_object_id();
$news_title = function_exists('get_field') ? get_field('news_section_title', $home_id) : '';
$news_title = $news_title ?: 'Latest news';

$news = get_posts([
  'post_type'      => 'news_item',   
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC',
]);
?>

<section class="news">
  <div class="news__inner">
    <h2 class="news__title"><?php echo esc_html($news_title); ?></h2>

    
    <div class="news__viewport" id="newsViewport">
      <div class="news__track" id="newsTrack">
        <?php foreach ($news as $post): setup_postdata($post); ?>
          <?php
            $id       = $post->ID;
            $img      = get_field('news_image', $id);      
            $heading  = (string) get_field('news_heading', $id);
            $excerpt  = (string) get_field('news_excerpt', $id);
            $date_raw = (string) get_field('news_date', $id);
            $date_out = $date_raw ? date_i18n(get_option('date_format'), strtotime($date_raw)) : get_the_date('', $id);
            $tag      = (string) get_field('news_tag', $id);
          ?>
          <article class="news__card">
            <div class="news__img">
              <?php if (!empty($img['ID'])) echo wp_get_attachment_image($img['ID'], 'large', false, ['loading'=>'lazy']); ?>
            </div>
            <?php if ($heading): ?><h3 class="news__heading"><?php echo esc_html($heading); ?></h3><?php endif; ?>
            <?php if ($excerpt): ?><p class="news__excerpt"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
            <div class="news__meta"><time datetime="<?php echo esc_attr($date_raw ?: get_the_date('c', $id)); ?>"><?php echo esc_html($date_out); ?></time></div>
            <?php if ($tag): ?><div class="news__tag"><?php echo esc_html($tag); ?></div><?php endif; ?>
          </article>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
    </div>

    <div class="news__more">
      <button type="button" class="btn-pill" id="newsMoreBtn">More news</button>
    </div>
  </div>
</section>



<?php
// ===== Blog teaser =====
$home_id = (int) get_option('page_on_front') ?: get_queried_object_id();

$title     = get_field('blog_teaser_title',   $home_id);
$image     = get_field('blog_teaser_image',   $home_id);   
$subtitle  = get_field('blog_teaser_subtitle',$home_id);
$text1     = get_field('blog_teaser_text1',   $home_id);
$text2     = get_field('blog_teaser_text2',   $home_id);
$btn_label = get_field('blog_teaser_button_label', $home_id) ?: 'See all posts';
$btn_link  = get_field('blog_teaser_button_link',  $home_id);
?>

<section class="blogteaser">
  <div class="blogteaser__inner">

    <div class="blogteaser__left">
      <?php if ($title): ?>
        <h2 class="blogteaser__title"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>

      <?php if (!empty($image['ID'])): ?>
        <div class="blogteaser__img">
          <?php echo wp_get_attachment_image($image['ID'], 'large', false, ['loading'=>'lazy']); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="blogteaser__right">
      <?php if ($subtitle): ?>
        <h3 class="blogteaser__subtitle"><?php echo esc_html($subtitle); ?></h3>
      <?php endif; ?>

      <?php if ($text1): ?>
        <p class="blogteaser__p"><?php echo nl2br(esc_html($text1)); ?></p>
      <?php endif; ?>

      <?php if ($text2): ?>
        <p class="blogteaser__p"><?php echo nl2br(esc_html($text2)); ?></p>
      <?php endif; ?>
    </div>

    <?php if ($btn_link): ?>
      <div class="blogteaser__cta">
        <a class="btn-pill" href="<?php echo esc_url($btn_link); ?>">
          <?php echo esc_html($btn_label); ?>
        </a>
      </div>
    <?php endif; ?>

  </div>
</section>



<?php
// ===== Partners block  =====
$home_id  = (int) get_option('page_on_front') ?: get_queried_object_id();

$p_title  = get_field('partners_title',  $home_id);
$p_image  = get_field('partners_image',  $home_id);   
$p_cap    = get_field('partners_caption',$home_id);
?>

<section class="partners">
  <div class="partners__inner">

    <div class="partners__frame">
      <?php if (!empty($p_image['ID'])): ?>
        <?php echo wp_get_attachment_image($p_image['ID'], 'full', false, ['loading'=>'lazy']); ?>
      <?php endif; ?>
      <?php if ($p_title): ?>
        <div class="partners__title"><?php echo esc_html($p_title); ?></div>
      <?php endif; ?>
    </div>

    <?php if ($p_cap): ?>
      <p class="partners__caption">
        <?php echo esc_html($p_cap); ?>
      </p>
    <?php endif; ?>

  </div>
</section>
</main>




<section class="fp-testimonials">
  <h2>Testimonials</h2>

  <?php
  $q = new WP_Query([
    'post_type'=>'testimonial',
    'post_status'=>'publish',
    'posts_per_page'=>6,
    'orderby'=>'date',
    'order'=>'DESC',
  ]);
  if ($q->have_posts()):
    echo '<div class="fp-list">';
    while($q->have_posts()): $q->the_post();
      $title = get_the_title();
      $content = get_the_content();
      echo '<article class="fp-item">';
      echo '<h4>'.esc_html($title).'</h4>';
      echo '<div class="fp-content">'.wp_kses_post(wpautop($content)).'</div>';
      echo '</article>';
    endwhile;
    echo '</div>';
    wp_reset_postdata();
  else:
    echo '<p>No testimonials yet.</p>';
  endif;
  ?>

  <?php if (isset($_GET['t_ok']) && $_GET['t_ok']==='1'): ?>
    <div class="fp-notice">Thank you! Your testimonial was submitted for review.</div>
  <?php endif; ?>

  <div class="fp-form">
    <?php if (is_user_logged_in()): ?>
      <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <p><label>Title<br><input type="text" name="t_title" required></label></p>
        <p><label>Message<br><textarea name="t_text" rows="6" required></textarea></label></p>
        <?php wp_nonce_field('t_submit_action','t_nonce'); ?>
        <input type="hidden" name="action" value="t_submit">
        <p><button type="submit">Send</button></p>
      </form>
    <?php else: ?>
      <p>Please <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">log in</a> or
      <a href="<?php echo esc_url(function_exists('wp_registration_url')? wp_registration_url(): (wp_login_url().'?action=register')); ?>">register</a> to submit a testimonial.</p>
    <?php endif; ?>
  </div>
</section>










<?php get_footer(); ?>
