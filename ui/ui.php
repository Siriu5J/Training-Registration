<?php
class training_registration_ui {
    // Helpful tools
    protected $tools;

    public function __construct() {
        require_once(ER_PLUGIN_DIR . '/includes/tools.php');
        $this->tools = new training_registration_tools();
    }

    /*
    STAFF PROFILE REGISTRATION FORM
     */
    public function staffFormCreation() {
        $username = wp_get_current_user()->user_login;  // Get Current username for school name

        // Insert information to database for staff registration
        if ($_POST['create_staff']) {
            global $wpdb;
            $showProfileMessage = true;

            $staff_table = ER_STAFF_PROFILE;
            $first_name = $_POST['first_name'];
            $last_name  = $_POST['last_name'];
            $cn_name    = $_POST['cn_name'];
            $sex        = $_POST['sex'];
            $age        = $_POST['age'];
            $email      = $_POST['email'];
            $phone      = $_POST['phone'];

            $position   = $_POST['position'];
            $lc         = $_POST['lc'];

            $t_exp      = $_POST['t-exp'];
            $cec_exp    = $_POST['cec-exp'];

            $degree     = $_POST['degree'];
            $grad_year  = $_POST['grad-year'];
            $major      = $_POST['major'];
            $minor      = $_POST['minor'];
            $institution= $_POST['institution'];

            $comment    = $_POST['comment'];
            $school     = $_POST['school'];

            // Check Duplicate
            if ($wpdb->get_var("SELECT COUNT(*) FROM $staff_table WHERE `first_name` = \"$first_name\" AND `last_name` = \"$last_name\" AND `school` = \"$school\" AND `phone` = $phone") == 0) {
                $success = $wpdb->insert($staff_table, array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "cn_name" => $cn_name,
                    "sex" => $sex,
                    "age" => $age,
                    "school" => $school,
                    "email" => $email,
                    "phone" => $phone,

                    "pos" => $position,
                    "lc" => $lc,
                    "training_exp" => $t_exp,
                    "cec_exp" => $cec_exp,

                    "degree" => $degree,
                    "grad_year" => $grad_year,
                    "major" => $major,
                    "minor" => $minor,
                    "institution" => $institution,

                    "comment" => $comment,
                ));

                if ($success) {
                    ?>
                    <div style="background-color: #5ac18e; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Profile for <?php echo $first_name.' '.$last_name ?> created</div>
                    <?php
                } else {
        // TODO: add site admin
        ?>
        <div style="background-color: #ff4040; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Cannot create staff profile. Please contact the Site Admin for support.</div>
        <?php
    }
    }else {
        ?>
        <div style="background-color: #ff4040; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Staff, <?php echo $first_name.' '.$last_name; ?>, already exist in record!</div>
        <?php
    }
    }

    ?>
    <form id="staff-profile" name="staff-profile" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
        <p>Fill out all fields marked with *</p>
        <strong>Part 1: Personal Information</strong><br><br>
        <label for="first_name">First Name*</label><input type="text" name="first_name" id="first_name" required/>
        <br/>
        <label for="last_name">Last Name*</label><input type="text" name="last_name" id="last_name" required/>
        <br/>
        <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Name in Native Language</label><input type="text" name="cn_name" id="cn_name"/>
        <br/>
        <label for="sex">Sex*</label>
        <input type="radio" name="sex" value="M" id="M" required/>M
        <input type="radio" name="sex" value="F" id="F" required/>F
        <br/><br>
        <label for="age">Age*</label>
        <select id="age" name="age" required>
            <option selected disabled>--</option>
            <option value="18-25">18-25</option>
            <option value="26-35">26-35</option>
            <option value="36-45">36-45</option>
            <option value="45+">45 or above</option>
        </select>
        <br/><br>
        <label for="email">Email*</label> <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required/>
        <br/>
        <label for="phone">Phone*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required/>
        <br/>
        <br><hr>
        <strong>Part 2: Current LC Service</strong><br><br>
        <label for="position">Position*</label>
        <select id="position" name="position" required>
            <option selected disabled>--</option>
            <option value="Administrator">Administrator</option>
            <option value="Principal">Principal</option>
            <option value="Supervisor">Supervisor</option>
            <option value="Monitor">Monitor</option>
            <option value="Others">Others</option>
        </select>
        <br/><br>
        <label for="lc">Learning Center*</label>
        <select id="lc" name="lc" required>
            <option selected disabled>--</option>
            <option value="Kindergarten">Kindergarten</option>
            <option value="ABC's">ABC's</option>
            <option value="Lower LC">Lower LC</option>
            <option value="Upper LC">Upper LC</option>
            <option value="Not in LC">Not in LC</option>
        </select>
        <br />
        <br><hr>
        <strong>Part 3: Training Experience</strong><br><br>
        <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Number of Summer Training Attended*</label><input type="number" name="t-exp" id="t-exp" min="0" required/>
        <br />
        <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="cec-exp">Number of Educators' Conventions Attended*</label><input type="number" name="cec-exp" id="cec-exp" min="0" required/>
        <br />
        <br><hr>
        <strong>Part 4: Educational Attainment</strong><br><br>
        <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Degree Obtained*</label>
        <select id="degree" name="degree" required>
            <option selected disabled>--</option>
            <option value="Elementary">Elementary</option>
            <option value="Middle School">Middle School</option>
            <option value="High School">High School</option>
            <option value="Associates">Associates</option>
            <option value="Bachelors">Bachelors</option>
            <option value="Masters">Masters</option>
            <option value="Doctorate">Doctorate</option>
        </select>
        <br/><br>
        <label for="grad-year">Year of Graduation*</label><input type="number" name="grad-year" id="grad-year" min="1950" step="1" required/>
        <br />
        <label for="major">Major</label><input class="er_input" type="text" name="major" id="major"/>
        <br />
        <label for="minor">Minor</label><input class="er_input" type="text" name="minor" id="minor" />
        <br />
        <label for="institution">Institution*</label><input class="er_input" type="text" name="institution" id="institution" required/>
        <br /><br><hr>
        <label for="comment">Comment</label><textarea name="comment" id="comment" cols="45" rows="5"></textarea>
        <br />
        <input type="hidden" name="school" value="<?php echo $username; ?>">
        <br/>
        <input type="submit" name="create_staff" id="create_staff" value="Create" />
        <input type="reset">
        <br />
    </form>
    <?php
    }

    /*
    REGISTER TO TRAINING
     */
    public function eventRegistration() {
        global $wpdb;
        $event_table = ER_EVENT_LIST;
        $staff_table = ER_STAFF_PROFILE;
        $reg_table = ER_REGISTRATION_LIST;
        $time_now = current_time('mysql');

        // Take care of the form
        if ($_POST['reg-training']) {
            $event = $_POST['training'];
            $staff = $_POST['staff'];

            // Make sure people don't try to submit "--"
            if ($event != '' && $staff != '') {

                // Double check the availability of the training
                $training = $wpdb->get_row("SELECT * FROM $event_table WHERE id = $event");
                if ($training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0)) {

                    // check for duplicate entries
                    if ($wpdb->get_var("SELECT COUNT(*) FROM $reg_table WHERE `staff` = $staff AND `event_id` = $event") == 0) {
                        $wpdb->insert($reg_table, array(
                            "event_id" => $event,
                            "staff"    => $staff,
                            "reg_time" => $time_now,
                            "school"   => $_POST['school'],
                            "comment"  => $_POST['comment'],
                        ));
                        $wpdb->update($event_table, array(
                            "num_reg" => $training->num_reg + 1,
                        ), array(
                            "id" => $event,
                        ));
                        ?>
                        <div style="background-color: #5ac18e; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;"><?php echo $this->tools->idtoName($staff) ?> is successfully registered to <?php echo $training->event_name.' at '.$training->location ?></div>
                        <?php
                    } else {
                        ?>
                        <div style="background-color: #ff4040; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;"><?php echo $this->tools->idtoName($staff) ?> has already been registered to <?php echo $training->event_name.' at '.$training->location ?>. </div>
                        <?php
                    }
                } else {
                    ?>
                    <div style="background-color: #ff4040; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Staff cannot be registered. Try to refresh the page and try again.</div>
                    <?php
                }
            } else {
                ?>
                <div style="background-color: #ff4040; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">You must select a training AND a staff to register.</div>
                <?php
            }


        }

        $username = wp_get_current_user()->user_login;

        // Only show trainings that are not started
        $trainings_to_show = $wpdb->get_results($wpdb->prepare("SELECT * FROM $event_table WHERE `start_time` > '$time_now' AND `activated` = 1"));

        if (count($trainings_to_show) != 0) {

            // Get preference on whether or not to show available seats
            $show_available = get_option( 'show_availability', 0 );

            ?>
            <div>
                <!-- Show all trainings that has not yet been started -->
                <table>
                    <?php
                    foreach ($trainings_to_show as $training) {
                        ?>
                        <tr>
                            <th colspan="3"><?php echo $training->event_name.' ('.$this->tools->availability($training).')' ?></th>
                            <th style="width: 20%">Location</th>
                            <td style="width: fit-content" colspan="2"><?php echo $training->location ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <th>Registration Date</th>
                            <td><?php echo date("Y-m-d", strtotime($training->open_time)).' to '. date("Y-m-d", strtotime($training->close_time))?></td>
                            <th style="width: 20%">Training Date</th>
                            <td><?php echo date("Y-m-d", strtotime($training->start_time)).' to '. date("Y-m-d", strtotime($training->end_time))?></td>
                        </tr>
                        <?php
                        // Reflect user settings
                        if ($show_available == 1) {
                            ?>
                            <tr>
                                <td></td>
                                <th>Available Seats</th>
                                <td colspan="3"><?php echo $this->tools->spotsOpen($training->max, $training->num_reg); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td></td>
                            <th>Information</th>
                            <td colspan="3"><?php echo $training->comment; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

            <!-- Form for registering into training -->
            <form id="reg-event" name="reg-event" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                <label for="training" style="float: left; width: 30%;">Select Training:</label>
                <select id="training" name="training" required>
                    <option selected disabled>Select a training</option>
                    <?php
                    foreach($trainings_to_show as $training) {
                        // Only trainings that are within registration time slot AND (isn't full OR allows overflow OR has no cap) can be selected.
                        if ($training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0)) {

                            echo '<option value="'.$training->id.'">'.$training->event_name.' at '.$training->location.'</option>';
                        }
                    }
                    ?>
                </select>
                <br>
                <label for="staff" style="float: left; width: 30%;">Staff List</label>
                <select id="staff" name="staff">
                    <option selected disabled >Select a staff</option>
                    <?php
                    // Get all staff profiles of a school
                    $staff_available = $wpdb->get_results($wpdb->prepare("SELECT * FROM $staff_table  WHERE school = '$username'"));

                    foreach ($staff_available as $staff) {
                        echo '<option value="'.$staff->id.'">'. $this->tools->idtoName($staff->id).'</option>';
                    }
                    ?>
                </select>
                <br>
                <label for="comment">Comment: </label><textarea name="comment" id="comment"></textarea>
                <input type="hidden" name="school" value="<?php echo $username; ?>">
                <br><br><input type="submit" name="reg-training" id="reg-training" value="Register"/><br><br>
            </form>
            <?php

        } else {    // Show a different message if there are no trainings to register
            ?>
            <div align="center">
                <h3 align="center">No trainings are available now. Check again later.</h3>
            </div>
            <?php
        }

    }

    /*
MANAGE STAFF PROFILES
 */
    function viewEditStaff() {
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $reg_table = ER_REGISTRATION_LIST;
        $event_table = ER_EVENT_LIST;
        $username = wp_get_current_user()->user_login;
        $time_now = current_time('mysql');

        // Update the database after editing profile
        if ($_POST['update-profile']) {
            $staff_table = ER_STAFF_PROFILE;
            $wpdb->update($staff_table, array(
                "first_name"    =>  $_POST['first_name'],
                "last_name"     =>  $_POST['last_name'],
                "cn_name"       =>  $_POST['cn_name'],
                "sex"           =>  $_POST['sex'],
                "age"           =>  $_POST['age'],
                "school"        =>  $_POST['school'],
                "email"         =>  $_POST['email'],
                "phone"         =>  $_POST['phone'],

                "pos"           =>  $_POST['position'],
                "lc"            =>  $_POST['lc'],
                "training_exp"  =>  $_POST['t-exp'],
                "cec_exp"       =>  $_POST['cec-exp'],

                "degree"        =>  $_POST['degree'],
                "grad_year"     =>  $_POST['grad-year'],
                "major"         =>  $_POST['major'],
                "minor"         =>  $_POST['minor'],
                "institution"   =>  $_POST['institution'],

                "comment"       =>  $_POST['comment'],
            ), array(
                "id"            =>  $_POST['id'],
            ));

            ?>
            <div style="background-color: #5ac18e; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Staff Profile Updated</div>
            <?php
        }

        // Withdraw from a training
        if ($_POST['confirm-remove']) {
            $trainings_to_remove = $_POST['training-id'];

            foreach($trainings_to_remove as $training) {
                // Remove staff from the registration record
                $wpdb->delete($reg_table, array(
                    'event_id' => $training,
                    'staff'    => $_POST['staff_id'],
                ));

                // Open up spots in event list available numbers
                $wpdb->update(ER_EVENT_LIST, array(
                    'num_reg' => $wpdb->get_var("SELECT `num_reg` FROM $event_table WHERE `id` = $training") - 1,
                ), array (
                    'id' => $training,
                ));
            }
            ?>
            <div style="background-color: #5ac18e; color: white; padding: 15pt; border-radius: 5px; position: initial; left: 50%;">Registration(s) Cancelled</div>
            <?php
        }

        // Don't show table if there are no staffs
        if ($wpdb->get_var("SELECT COUNT(*) FROM $staff_table WHERE `school` = '$username'") != 0) {
            ?>
            <table>
                <tr>
                    <th style="width: fit-content"></th>
                    <th style="width: fit-content">Name</th>
                    <th style="width: fit-content">Sex</th>
                    <th style="width: fit-content">Position</th>
                    <th style="width: fit-content">Email</th>
                    <th style="width: max-content">Upcoming Training(s) Registered</th>
                </tr>
                <form id="select-staff" name="select-staff" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                    <?php
                    // List staff
                    $all_staff = $wpdb->get_results("SELECT * FROM $staff_table WHERE `school` = '$username'");
                    foreach ($all_staff as $staff) {

                        // List the upcoming trainings that this staff is registered to (don't include past trainings
                        $trainings = $wpdb->get_results($wpdb->prepare("SELECT `event_id` FROM $reg_table WHERE `staff` = $staff->id")); // List all trainings related to the staff then filter

                        $training_registered = "<ul style='margin:0'>"; // The string we are going to work on

                        foreach ($trainings as $training) { // add each event name to $training_registered as list item
                            // Only show the training if it is upcoming
                            if ($time_now < $wpdb->get_var("SELECT `start_time` FROM $event_table WHERE `id` = $training->event_id")) {
                                $training_registered .= '<li>'.$wpdb->get_var("SELECT `event_name` FROM $event_table WHERE `id` = $training->event_id").'</li>';
                            }
                        }

                        $training_registered .= '</ul>';

                        ?>
                        <tr>
                            <td><input type="radio" name="select" value="<?php echo $staff->id ?>" required/></td>
                            <td><?php echo $this->tools->idtoName($staff->id); ?></td>
                            <td><?php echo $staff->sex; ?></td>
                            <td><?php echo $staff->pos; ?></td>
                            <td><?php echo $staff->email; ?></td>
                            <td><?php if ($training_registered != "<ul style='margin:0'></ul>") {echo $training_registered;} else {echo "No Trainings Registered";} ?></td>   <!--show no trainings registered when appropriate -->
                        </tr>
                        <?php
                    }
                    ?>
            </table>
            <br>
            <div align="center" style="">
                <input style="float: left; width: fit-content" type="submit" name="edit-reg" id="edit-reg" value="Cancel Staff Registration" />
                <input style="float: right; width: fit-content" type="submit" name="edit-profile" id="edit-profile" value="Edit Staff Profile" />
            </div>
            </form>
            <?php
        } else {
            ?>
            <div align="center">
                <h3 align="center">No Staff Found</h3>
            </div>
            <?php
        }

        // Edit Staff profile
        if ($_POST['edit-profile']) {
            $staff_id = $_POST['select'];
            $profile = $wpdb->get_row("SELECT * FROM $staff_table WHERE `id` = $staff_id");

            ?>
            <form id="staff-profile" name="staff-profile" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                <br>
                <hr>
                <h3>Editing Staff Profile</h3>
                <p>Fill out all fields marked with *</p>
                <strong>Part 1: Personal Information</strong><br><br>
                <label for="first_name">First Name*</label><input type="text" name="first_name" id="first_name" value="<?php echo $profile->first_name ?>" required/>
                <br/>
                <label for="last_name">Last Name*</label><input type="text" name="last_name" id="last_name" value="<?php echo $profile->last_name ?>" required/>
                <br/>
                <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Name in Native Language</label><input type="text" name="cn_name" id="cn_name" value="<?php echo $profile->cn_name ?>"/>
                <br/>
                <label for="sex">Gender/Sex*</label>
                <input type="radio" name="sex" value="M" id="M" required <?php if ($profile->sex == 'M') {echo 'checked';} ?>/>M
                <input type="radio" name="sex" value="F" id="F" required <?php if ($profile->sex == 'F') {echo 'checked';} ?>/>F
                <br/><br>
                <label for="age">Age*</label>
                <select id="age" name="age" required >
                    <option value="18-25" <?php if ($profile->age == '18-25') {echo 'selected';} ?>>18-25</option>
                    <option value="26-35" <?php if ($profile->age == '26-35') {echo 'selected';} ?>>26-35</option>
                    <option value="36-45" <?php if ($profile->age == '36-45') {echo 'selected';} ?>>36-45</option>
                    <option value="45+" <?php if ($profile->age == '45+') {echo 'selected';} ?>>45 or above</option>
                </select>
                <br/><br>
                <label for="email">Email*</label> <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required value="<?php echo $profile->email ?>"/>
                <br/>
                <label for="phone">Phone*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required value="<?php echo $profile->phone ?>"/>
                <br/>
                <br><hr>
                <strong>Part 2: Current LC Service</strong><br><br>
                <label for="position">Position*</label>
                <select id="position" name="position" required>
                    <option value="Administrator" <?php if ($profile->pos == 'Administrator') {echo 'selected';} ?>>Administrator</option>
                    <option value="Principal" <?php if ($profile->pos == 'Principal') {echo 'selected';} ?>>Principal</option>
                    <option value="Supervisor" <?php if ($profile->pos == 'Supervisor') {echo 'selected';} ?>>Supervisor</option>
                    <option value="Monitor" <?php if ($profile->pos == 'Monitor') {echo 'selected';} ?>>Monitor</option>
                    <option value="Others" <?php if ($profile->pos == 'Others') {echo 'selected';} ?>>Others</option>
                </select>
                <br/><br>
                <label for="lc">Learning Center*</label>
                <select id="lc" name="lc" required>
                    <option value="Kindergarten" <?php if ($profile->lc == 'Kindergarten') {echo 'selected';} ?>>Kindergarten</option>
                    <option value="ABC's" <?php if ($profile->lc == 'ABC\'s') {echo 'selected';} ?>>ABC's</option>
                    <option value="Lower LC" <?php if ($profile->lc == 'Lower LC') {echo 'selected';} ?>>Lower LC</option>
                    <option value="Upper LC" <?php if ($profile->lc == 'Upper LC') {echo 'selected';} ?>>Upper LC</option>
                    <option value="Upper LC" <?php if ($profile->lc == 'Not in LC') {echo 'selected';} ?>>Not in LC</option>
                </select>
                <br />
                <br><hr>
                <strong>Part 3: Training Experience</strong><br><br>
                <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Number of Summer Training Attended*</label><input type="number" name="t-exp" id="t-exp" min="0" required value="<?php echo $profile->training_exp ?>"/>
                <br />
                <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="cec-exp">Number of Educators' Conventions Attended*</label><input type="number" name="cec-exp" id="cec-exp" min="0" required value="<?php echo $profile->cec_exp ?>"/>
                <br />
                <br><hr>
                <strong>Part 4: Educational Attainment</strong><br><br>
                <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Degree Obtained*</label>
                <select id="degree" name="degree" required>
                    <option value="Elementary" <?php if ($profile->degree == 'Elementary') {echo 'selected';} ?>>Elementary</option>
                    <option value="Middle School" <?php if ($profile->degree == 'Middle School') {echo 'selected';} ?>>Middle School</option>
                    <option value="High School" <?php if ($profile->degree == 'High School') {echo 'selected';} ?>>High School</option>
                    <option value="Associates" <?php if ($profile->degree == 'Associates') {echo 'selected';} ?>>Associates</option>
                    <option value="Bachelors" <?php if ($profile->degree == 'Bachelors') {echo 'selected';} ?>>Bachelors</option>
                    <option value="Masters" <?php if ($profile->degree == 'Masters') {echo 'selected';} ?>>Masters</option>
                    <option value="Ph.D" <?php if ($profile->degree == 'Doctorate') {echo 'selected';} ?>>Doctorate</option>
                </select>
                <br/><br>
                <label for="grad-year">Year of Graduation*</label><input type="number" name="grad-year" id="grad-year" min="1950" required value="<?php echo $profile->grad_year ?>"/>
                <br />
                <label for="major">Major</label><input class="er_input" type="text" name="major" id="major" value="<?php echo $profile->major; ?>"/>
                <br />
                <label for="minor">Minor</label><input class="er_input" type="text" name="minor" id="minor" value="<?php echo $profile->minor; ?>"/>
                <br />
                <label for="institution">Institution*</label><input class="er_input" type="text" name="institution" id="institution" required value="<?php echo $profile->institution; ?>"/>
                <br /><br><hr>
                <label for="comment">Comment</label><textarea name="comment" id="comment" cols="45" rows="5"><?php echo $profile->comment ?></textarea>
                <br />
                <input type="hidden" name="school" value="<?php echo $username; ?>">
                <input type="hidden" name="id" value="<?php echo $staff_id ?>">
                <br/>
                <input type="submit" name="update-profile" id="update-profile" value="Update Profile" />
                <input type="submit" name="update-cancel" id="update-cancel" value="Cancel" />
                <br />
            </form>
            <?php
        }

        // Edit Staff Registration
        if ($_POST['edit-reg']) {
            $staff_id = $_POST['select'];
            $trainings_registered = $wpdb->get_results($wpdb->prepare("SELECT `event_id` FROM $reg_table WHERE `staff` = $staff_id")); // All trainings the user registered to
            ?>
            <br>
            <hr>
            <?php
            if ($this->tools->hasRemovables($staff_id)) {
                ?>
                <h4>Cancel Registrations for <?php echo $this->tools->idtoName($staff_id) ?>:</h4>
                <p><b>Important Notice:</b><br>Although it is possible to withdraw from a training here even after the training registration is closed, please <b>ALWAYS</b> notify the training organizer before doing so. To withdraw from a training, select the training(s) and click withdraw.</p>
                <div align="center">
                    <form id="staff-profile" name="staff-profile" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                        <select name="training-id[]" id="training-id" required multiple="multiple">
                            <?php
                            foreach ($trainings_registered as $training) {
                                // Only show open if it also shows on the list above, which are upcoming trainings
                                if ($wpdb->get_var("SELECT `start_time` FROM $event_table WHERE `id` = $training->event_id") > $time_now) {
                                    // Get information about the Training
                                    $training_info = $wpdb->get_row("SELECT * FROM $event_table WHERE `id` = $training->event_id");
                                    ?>
                                    <option value="<?php echo $training->event_id ?>"><?php echo $training_info->event_name.' at '.$training_info->location ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <br>
                        <input type="hidden" name="staff_id" value="<?php echo $staff_id ?>">
                        <input type="submit" name="confirm-remove" id="confirm-remove" value="Withdraw" /><br><br>
                    </form>
                    <form id="cancel" name="cancel" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                        <input type="submit" name="cancel" id="cancel" value="Cancel" />
                    </form>
                </div>
                <?php
            } else {
                ?>
                <h4>No registrations available for cancelling for <?php echo $this->tools->idtoName($staff_id) ?></h4>
                <?php
            }

        }
    }
}







