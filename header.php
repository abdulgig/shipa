 <style>
.tabletr{ background:#E5E5E5; text-align:center; padding:10px; width:100%;}
 #navigation ul{
        padding: 0;
        list-style: none;
       
    }
   #navigation ul li{
        display: inline-block;
        position: relative;
        line-height: 21px;
        text-align: left;
		
    }
    #navigation ul li a{
        display: block;
        padding: 8px 25px;
       
        text-decoration: none;
    }
   #navigation ul li a:hover{
        color: #fff;
        
    }
   #navigation ul li ul.dropdown{
        min-width: 100%; /* Set width of the dropdown */
        background: #0000ff;
        display: none;
        position: absolute;
        z-index: 999;
        left: 0;
    }
	 #navigation  ul li ul li a{        
        color: #fff !important;
        
    }
		 #navigation  ul li ul li a:hover{        
        color: #fff !important;
        
    }
   #navigation ul li:hover ul.dropdown{
        display: block;	/* Display the dropdown */
    }
   #navigation ul li ul.dropdown li{
        display: block;
    }

#topnav .navigation-menu > li > a { color:#FFFFFF;}
#topnav .navigation-menu > li > a:hover{color:#FFFFFF;}


</style>

<header id="topnav">
    <div class="topbar-main">
        <div class="container">

            <!-- Logo container-->
            <div class="logo">
                <!-- Text Logo -->
                <!--<a href="index.html" class="logo">-->
                <!--Zircos-->
                <!--</a>-->
                <!-- Image Logo -->
                <a href="index.html" class="logo">
                    <img src="assets/images/logo.png" alt="" height="30">
                </a>

            </div>
            <!-- End Logo container-->


            <div class="menu-extras">
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>
            <!-- end menu-extras -->

        </div> <!-- end container -->
    </div>
    <!-- end topbar-main -->

    <div class="navbar-custom">
        <div class="container">
            <div id="navigation">
                <!-- Navigation Menu-->
                <ul class="navigation-menu">
                    <li class="has-submenu">
                        <a href="inbound.php" class="color-white"><i class="mdi mdi-view-dashboard icon-white"></i>Inbound Reports</a>
                    </li>
                    <li class="has-submenu">
                       <a href="outbound.php" class="color-white"><i class="mdi mdi-phone icon-white"></i>Outbound Reports</a> 
                    </li>
                    <li class="has-submenu">
                       <a href="useragent.php" class="color-white"><i class="mdi mdi-account icon-white"></i>Live Agents</a>
                    </li>
					  <!--<li class="has-submenu">
                       <a href="callbycountry.php" class="color-white"><i class="mdi mdi-account icon-white"></i>Call By Country</a>
                    </li> -->
						<li class="dropdown"><a href="callbycountry.php"><i class="mdi mdi-account icon-white"></i>Calls By Country <span class="caret"></span></a>
						<ul class="dropdown">
						<li><a href="inbound.php">Inbound</a></li> 
						<li><a href="outbound.php">Outbound</a></li>						         
						</ul>
						</li>
                    <li class="has-submenu f-right">
                        <a href="#" class="color-white" id="time-now"></a>
                    </li>
                </ul>
                <!-- End navigation menu -->
            </div> <!-- end #navigation -->
        </div> <!-- end container -->
    </div> <!-- end navbar-custom -->
</header>