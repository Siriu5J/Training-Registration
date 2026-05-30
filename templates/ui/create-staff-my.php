<form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
    <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
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
    <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
    <br/>
    <input type="submit" name="create_staff" id="create_staff" value="Create" />
    <input type="reset">
    <br />
</form>
