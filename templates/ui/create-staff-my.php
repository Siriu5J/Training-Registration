<?php
/**
 * @var string $username
 * @var int $this_year
 */
?>
<div class="sot-tr-container">
    <div class="sot-tr-form">
        <h2 class="sot-tr-center">Create Staff Profile (SOTAM)</h2>
        
        <form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
            
            <p>Fields marked with * are required.</p>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 1: Personal Information</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="first_name">First Name*</label>
                        <input type="text" name="first_name" id="first_name" required/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="last_name">Last Name*</label>
                        <input type="text" name="last_name" id="last_name" required/>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="mid_name">Full Name (for Training Certificate)*</label>
                    <input type="text" name="mid_name" id="mid_name" required/>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label>Gender*</label>
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.25rem; height: 3rem; align-items: center;">
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="M" id="M" required/> Male
                            </label>
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="F" id="F" required/> Female
                            </label>
                        </div>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="cn_name">Religion*</label>
                        <input type="text" name="cn_name" id="cn_name" required/>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="degree">Highest Education Level*</label>
                        <select id="degree" name="degree" required>
                            <option value="" selected disabled>-- Select Level --</option>
                            <option value="SPM/O-Level">SPM/O-Level</option>
                            <option value="STPM/A-Level">STPM/A-Level</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Bachelor's Degree">Bachelor's Degree</option>
                            <option value="Masters' Degree">Masters' Degree</option>
                            <option value="PhD/Doctorate">PhD/Doctorate</option>
                        </select>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="phone">Mobile Number*</label>
                        <input type="tel" name="phone" id="phone" placeholder="Numbers only" required/>
                    </div>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 2: School Service</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="position">Position*</label>
                        <select id="position" name="position" required>
                            <option value="" selected disabled>-- Select Position --</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Principal">Principal</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="t-exp">Year of Last Training*</label>
                        <input type="number" name="t-exp" id="t-exp" min="2010" max="<?php echo $this_year ?>" required/>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="lc">Which training was attended above?*</label>
                    <select id="lc" name="lc" required>
                        <option value="" selected disabled>-- Select Training --</option>
                        <option value="Administrators' ">Administrators'</option>
                        <option value="Supervisors' ">Supervisors'</option>
                    </select>
                </div>
            </div>

            <div class="sot-tr-form-group">
                <label for="comment">Additional Comments (Optional)</label>
                <textarea name="comment" id="comment" rows="4"></textarea>
            </div>

            <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
            
            <div class="sot-tr-submit-row">
                <input type="submit" name="create_staff" id="create_staff" value="Create Profile" class="button button-primary" />
                <input type="reset" value="Reset Form" class="button" />
            </div>
        </form>
    </div>
</div>
