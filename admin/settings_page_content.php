<?php

class settings_page_content {
    public function overview() {
        ?>

        <h1>Event Registration Plugin V2</h1>
        <br>
        <p>V1 of this plugin is an unpublished beta version of this plugin. The V2 version is a rewritten version of V1, focusing on optimizing the codes and providing some minor visual update.</p>

        <?php
    }

    public function new_event() {
        ?>
        <h1>Create New Training Event</h1>
        <p>Please make sure that the start time is <strong>always</strong> before the end time. Also, the start time must be in the future to have the system recognize the training as an upcoming training.</p>
        <form id="new-event" name="new-event" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <label for="event-name">Training Name:<br></label><input class="er_input" type="text" name="event-name" id="event-name" required/>
            <br><br>
            <label for="open-date">Registration Open Date:<br></label><input class="er_input" type="datetime-local" name="open-date" id="open-date" value="<?php echo date("Y-m-d\TH:i", mktime(0,0)) ?>" required/>
            <br><br>
            <label for="close-date">Registration Close Date:<br></label><input class="er_input" type="datetime-local" name="close-date" id="close-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
            <br><br>
            <label for="start-date">Training Start Date:<br></label><input class="er_input" type="datetime-local" name="start-date" id="start-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
            <br><br>
            <label for="end-date">Training End Date:<br></label><input class="er_input" type="datetime-local" name="end-date" id="end-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
            <br><br>
            <label for="max">Max Registration (optional; leave blank for unlimited):<br></label><input class="er_input" type="number" name="max" id="max" min="0" step="1"/>
            <br><br>
            <label for="max-limit">Limit Maximum registration? &nbsp;&nbsp;&nbsp;</label><input class="er_input" type="checkbox" name="max-limit" id="max-limit" value="1"/>
            <br><br>
            <label for="location">Location:<br></label><input class="er_input" type="text" name="location" id="location" required/>
            <br><br>
            <label for="comment">Information on Training Event<br></label><textarea name="comment" id="comment" cols="45" rows="5"></textarea>
            <br><br>
            <label for="activated">Activated? &nbsp;&nbsp;&nbsp;</label><input class="er_input" type="checkbox" name="activated" id="activated" value="1"/>
            <br><br>
            <input type="submit" name="create_training" id="create_training" value="Create Training" />
        </form>
        <?php
    }
}