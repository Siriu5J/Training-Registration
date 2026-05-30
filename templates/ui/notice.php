<?php
/**
 * @var string $type
 * @var string $message
 */
?>
<div class="er-notice er-notice-<?php echo esc_attr($type); ?>">
    <?php echo esc_html($message); ?>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
</div>
