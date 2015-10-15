<?php
get_header();

$action =  isset($_GET['action']) ?  $_GET['action'] : '';
$title = "Member Login";

?>
<div class="row full-width"><hr/>
    <div class="small-4 large-4 large-offset-4 columns mp-login">
        	<?php
                if(isset($_GET['action']) && $_GET['action'] === 'failed')
                {
                    echo '<div class="alert-box alert">Invalid username or password.</div>';
                }
                if(isset($_GET['action']) && $_GET['action'] === 'reset')
                {
                    $login_url = home_url('/members/login/?action=reset');

                    if(isset($success_reset))
                    {
                        echo '<h2>Your password have been successfully reset. Kindy check your email for additional instructions.</h2>';
                    }
                    else
                    {
                        if(isset($error))
                        {
                            echo "<div class='alert-box alert'>$error</div>";
                        }

                        echo 
                            "<h2>Reset Password</h2>
                             <form action='$login_url' method='POST'>
                              <div>
                                   <label for='email_reset'>Please enter your email/username</label>
                                   <input type='text' id='email_reset' name='email_reset' size='50' maxlength='50'>
                                   <input type='submit' class='button' value='Reset Password'/>
                              </div>
                             </form>";
                    }

                }
                else
                {
                    echo "<h2>$title</h2>";
                    wp_login_form( $form_opts );
                }
        	?>
    </div>
    <div class="vs-md"></div>
</div>
<?php
get_sidebar();
get_footer();
?>
