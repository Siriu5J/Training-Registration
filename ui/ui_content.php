<?php


class ui_content
{
    public function create_staff_cn($username) {
        $this_year = date("Y");
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
            <label for="grad-year">Year of Graduation*</label><input type="number" name="grad-year" id="grad-year" min="1950" max="<?php echo $this_year ?>" step="1" required/>
            <br />
            <label for="major">Major</label><input class="er_input" type="text" name="major" id="major"/>
            <br />
            <label for="minor">Minor</label><input class="er_input" type="text" name="minor" id="minor" />
            <br />
            <label for="institution">Institution</label><input class="er_input" type="text" name="institution" id="institution"/>
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

    public function create_staff_my($username) {
        $this_year = date("Y");
        ?>
        <form id="staff-profile" name="staff-profile" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <p>Fill out all fields marked with *</p>
            <strong>Part 1: Personal Information</strong><br><br>
            <label for="first_name">First Name*</label><input type="text" name="first_name" id="first_name" required/>
            <br/>
            <label for="last_name">Last Name*</label><input type="text" name="last_name" id="last_name" required/>
            <br/>
            <!-- This is a customization for SOT MY. This will be using the mid_name field -->
            <label for="mid_name">Full Name (to appear on Training Certificate)*</label><input type="text" name="mid_name" id="mid_name" required/>
            <br/>
            <label for="sex">Gender*</label>
            <input type="radio" name="sex" value="M" id="M" required/>M
            <input type="radio" name="sex" value="F" id="F" required/>F
            <br/>
            <!-- This is a customization for SOT MY. Using the cn_name field -->
            <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Religion*</label><input type="text" name="cn_name" id="cn_name" required/>
            <br /><br />
            <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Education Level*</label>
            <!-- This is a customization for SOT MY. -->
            <select id="degree" name="degree" required>
                <option selected disabled>--</option>
                <option value="SPM/O-Level">SPM/O-Level</option>
                <option value="STPM/A-Level">STPM/A-Level</option>
                <option value="Diploma">Diploma</option>
                <option value="Bachelor&#39s Degree">Bachelor's Degree</option>
                <option value="Masters&#39 Degree">Masters' Degree</option>
                <option value="PhD/Doctorate">PhD/Doctorate</option>
            </select>
            <br/><br>
            <label for="phone">Mobile Number*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required/>
            <br/>
            <br><hr>
            <strong>Part 2: School Service</strong><br><br>
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
            <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Year of Last Training*</label><input type="number" name="t-exp" id="t-exp" min="2010" max="<?php echo $this_year ?>" required/>
            <br />
            <label for="lc">Which Training was attended above?*</label>
            <select id="lc" name="lc" required>
                <option selected disabled>--</option>
                <option value="Administrators&#39 ">Administrators'</option>
                <option value="Supervisors&#39 ">Supervisors'</option>
            </select>
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

    public function edit_staff_cn($username, $profile, $staff_id) {
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

    public function edit_staff_my($username, $profile, $staff_id) {
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
            <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Religion</label><input type="text" name="cn_name" id="cn_name" value="<?php echo $profile->cn_name ?>"/>
            <br/>
            <label for="sex">Gender*</label>
            <input type="radio" name="sex" value="M" id="M" required <?php if ($profile->sex == 'M') {echo 'checked';} ?>/>M
            <input type="radio" name="sex" value="F" id="F" required <?php if ($profile->sex == 'F') {echo 'checked';} ?>/>F
            <br/>
            <br/>
            <!-- This is a customization for SOT MY. Using the cn_name field -->
            <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Religion*</label><input type="text" name="cn_name" id="cn_name" required value="<?php echo $profile->cn_name ?>"/>
            <br />
            <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Degree Obtained*</label>
            <select id="degree" name="degree" required>
                <option value="SPM/O-Level" <?php if ($profile->degree == 'SPM/O-Level') {echo 'selected';} ?>>SPM/O-Level</option>
                <option value="STPM/A-Level" <?php if ($profile->degree == 'STPM/A-Level') {echo 'selected';} ?>>STPM/A-Level</option>
                <option value="Diploma" <?php if ($profile->degree == 'Diploma') {echo 'selected';} ?>>Diploma</option>
                <option value="Associates" <?php if ($profile->degree == 'Associates') {echo 'selected';} ?>>Associates</option>
                <option value="Bachelor&#39s Degree" <?php if ($profile->degree == 'Bachelor\\\'s Degree') {echo 'selected';} ?>>Bachelor's Degree</option>
                <option value="Masters&#39 Degree" <?php if ($profile->degree == 'Masters\\\' Degree') {echo 'selected';} ?>>Masters' Degree</option>
                <option value="PhD/Doctorate" <?php if ($profile->degree == 'PhD/Doctorate') {echo 'selected';} ?>>PhD/Doctorate</option>
            </select>
            <br />
            <label for="phone">Mobile Number*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required value="<?php echo $profile->phone ?>"/>
            <br/>
            <br><hr>
            <strong>Part 2: School Service</strong><br><br>
            <label for="position">Position*</label>
            <select id="position" name="position" required>
                <option value="Administrator" <?php if ($profile->pos == 'Administrator') {echo 'selected';} ?>>Administrator</option>
                <option value="Principal" <?php if ($profile->pos == 'Principal') {echo 'selected';} ?>>Principal</option>
                <option value="Supervisor" <?php if ($profile->pos == 'Supervisor') {echo 'selected';} ?>>Supervisor</option>
                <option value="Monitor" <?php if ($profile->pos == 'Monitor') {echo 'selected';} ?>>Monitor</option>
                <option value="Others" <?php if ($profile->pos == 'Others') {echo 'selected';} ?>>Others</option>
            </select>
            <br/><br>
            <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Year of Last Training*</label><input type="number" name="t-exp" id="t-exp" min="1950" required value="<?php echo $profile->grad_year ?>"/>

            <label for="lc">Which Training was attended above?*</label>
            <select id="lc" name="lc" required>
                <option value="Administrators' " <?php if ($profile->lc == 'Administrators\\\' ') {echo 'selected';} ?>>Administrators'</option>
                <option value="Supervisors' " <?php if ($profile->lc == 'Supervisors\\\' ') {echo 'selected';} ?>>Supervisors'</option>
            </select>
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
}