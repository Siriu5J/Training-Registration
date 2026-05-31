<?php
/**
 * @var string $username
 * @var int $this_year
 */
?>
<div class="sot-tr-container">
    <div class="sot-tr-form">
        <h2 class="sot-tr-center">Create Staff Profile</h2>
        
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

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="cn_name">Name in Native Language (Optional)</label>
                        <input type="text" name="cn_name" id="cn_name"/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label>Sex*</label>
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.25rem; height: 3rem; align-items: center;">
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="M" id="M" required/> Male
                            </label>
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="F" id="F" required/> Female
                            </label>
                        </div>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="age">Age Range*</label>
                        <select id="age" name="age" required>
                            <option value="" selected disabled>-- Select Age --</option>
                            <option value="18-25">18-25</option>
                            <option value="26-35">26-35</option>
                            <option value="36-45">36-45</option>
                            <option value="45+">45 or above</option>
                        </select>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="email">Email Address*</label>
                        <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required/>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="phone">Phone Number*</label>
                    <input type="tel" name="phone" id="phone" placeholder="Numbers only" required/>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 2: Current LC Service</h3>
                
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
                        <label for="lc">Learning Center*</label>
                        <select id="lc" name="lc" required>
                            <option value="" selected disabled>-- Select LC Type --</option>
                            <option value="Kindergarten">Kindergarten</option>
                            <option value="Lower LC">Lower LC</option>
                            <option value="Upper LC">Upper LC</option>
                            <option value="Not in LC">Not in LC</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 3: Training Experience</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="t-exp">Summer Trainings Attended*</label>
                        <input type="number" name="t-exp" id="t-exp" min="0" required/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="cec-exp">Educators' Conventions Attended*</label>
                        <input type="number" name="cec-exp" id="cec-exp" min="0" required/>
                    </div>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 4: Educational Attainment</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="degree">Highest Degree Obtained*</label>
                        <select id="degree" name="degree" required>
                            <option value="" selected disabled>-- Select Degree --</option>
                            <option value="Elementary">Elementary</option>
                            <option value="Middle School">Middle School</option>
                            <option value="High School">High School</option>
                            <option value="Associates">Associates</option>
                            <option value="Bachelors">Bachelors</option>
                            <option value="Masters">Masters</option>
                            <option value="Doctorate">Doctorate</option>
                        </select>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="grad-year">Year of Graduation*</label>
                        <input type="number" name="grad-year" id="grad-year" min="1950" max="<?php echo $this_year ?>" step="1" required/>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="major">Major (Optional)</label>
                        <input type="text" name="major" id="major"/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="institution">Institution (Optional)</label>
                        <input type="text" name="institution" id="institution"/>
                    </div>
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
