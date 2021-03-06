<div class="navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Navbar Logo -->
            {logo}
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li>{home_link}</li>
                <li>{members_link}</li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-keyboard-o"></i>&nbsp;<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <!-- BEGIN {language_pack} -->
                        <li>{pack}</li>
                        <!-- END {language_pack} -->
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i> {lang->frontend->text_account} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <!-- if {auth->logged_in} -->
                        <li>{profile_link}</li>
                        <li role="presentation" class="divider"></li>
                        <li>{sign_out_link}</li>
                        <!-- ELSE -->
                        <li>{sign_in_link}</li>
                        <li>{sign_up_link}</li>
                        <!-- END -->
                    </ul>
                </li>
            </ul>
        </div><!-- nav-collapse -->
    </div><!-- container -->
</div><!-- nav -->