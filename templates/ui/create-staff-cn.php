<form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
    <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
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
    <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
    <br/>
    <input type="submit" name="create_staff" id="create_staff" value="Create" />
    <input type="reset">
    <br />
</form>
