<div class="wrap">
    <h1 class="wp-heading-inline">Manage Registrations</h1>
    <?php if (count($trainings) > 0) : ?>
    <p>All the registrations of <b>upcoming AND activated trainings</b> can be seen here. To see the registration list of a particular training, click on the name of the training. You can use the bulk action to remove trainee(s) from a training.</p>
    <hr class="wp-header-end">
    <table style="width:100%; border-collapse: collapse">
        <tr style="outline: thin solid; text-align: left;">
            <th style="width: 30%">Training Name</th>
            <th style="width: 15%">Location</th>
            <th style="width: 10%">Training Start Time</th>
            <th style="width: 10%">Training End Time</th>
            <th style="width: 13%">Available Slots</th>
            <th style="width: 13%">Registration Stat</th>
        </tr>
        <?php
        $trainingNumber = 0;
        foreach ($trainings as $training) {
            $trainingNumber++;
            ?>
            <tr <?php if ($trainingNumber % 2 == 0) { echo 'bgcolor="#A9A9A9"'; } ?> style="height: 25pt;">
                <td><a href="#<?php echo esc_attr($training->id); ?>"><?php echo esc_html($training->event_name) ?></a></td>
                <td><?php echo esc_html($training->location) ?></td>
                <td><?php echo esc_html(date("Y-m-d", strtotime($training->start_time))) ?></td>
                <td><?php echo esc_html(date("Y-m-d", strtotime($training->end_time))) ?></td>
                <td><?php echo esc_html($tools->spotsOpen($training->id)) ?></td>
                <td><?php echo esc_html($tools->availability($training)) ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <br><br><hr/>
    <?php
    foreach ($trainings as $training) {
        if ($my_mode == 1) {
            $reg_table = new StaffRegTableMY($tools);
        } else {
            $reg_table = new StaffRegTableCN($tools);
        }
        $reg_table->set_event_id($training->id);
        $reg_table->prepare_items();
        ?>
        <div class="wrap" id="<?php echo esc_attr($training->id); ?>">
            <h3>Registrations for <?php echo esc_html($training->event_name . ' at ' . $training->location); ?></h3>
            <form id="staff-reg-<?php echo $training->id ?>" method="GET" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                <?php wp_nonce_field('staff_reg_nonce', 'reg_nonce_field'); ?>
                <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
                <?php $reg_table->display(); ?>
            </form>
        </div>
        <br><br><hr/>
        <?php
    }
    ?>
    <?php else : ?>
    <div style="display: contents; justify-content: center;">
        <h3 align="center">No Activated and Upcoming Trainings Found!<br>
            <p align="center">This page will only allow you to manage registrations of activated and upcoming (start date set to time in the future) trainings.<br>Make sure trainings you want to manage fulfill both requirements.</p>
    </div>
    <?php endif; ?>
</div>
