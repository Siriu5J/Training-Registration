<?php
/**
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var int $id
 * @var \WP_List_Table $reg_table
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Registrations for <?php echo esc_html($tools->getFieldById(ER_EVENT_LIST, 'event_name', $id).' at '. $tools->getFieldById(ER_EVENT_LIST, 'location', $id)) ?></h1>
    <p>Please use the browser search feature to search this list (Press Ctrl+f or Command+f). You can use the bulk action to remove trainee(s) from a training.</p>
    <hr class="wp-header-end">

    <form id="staff-reg" method="GET" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <?php wp_nonce_field('staff_reg_nonce', 'reg_nonce_field'); ?>
        <input type="hidden" name="event-id" value="<?php echo esc_attr($id) ?>" />
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
        <?php $reg_table->display(); ?>
    </form>
</div>
