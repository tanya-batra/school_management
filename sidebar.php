<?php 
date_default_timezone_set('Asia/Kolkata');
?> <!-- Sidebar navigation-->
 <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <!--<li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            </li>-->
            <li class="sidebar-item">
              <a class="sidebar-link" href="dashboard.php" aria-expanded="false">
                <span>
                  <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>

			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($viewFees)) { echo $viewFees; }?>" href="add-class-and-fees.php" aria-expanded="false">
                <span>
                  <i class="ti ti-book"></i>
                </span>
                <span class="hide-menu">Add Class & Fees</span>
              </a>
            </li>			
			
            <li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($addStudent)) { echo $addStudent; }?>" href="add-student.php" aria-expanded="false">
                <span>
                  <i class="ti ti-user"></i>
                </span>
                <span class="hide-menu">Add Student</span>
              </a>
            </li>
			
			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($viewStudents)) { echo $viewStudents; }?>" href="view-students.php" aria-expanded="false">
                <span>
                  <i class="ti ti-search"></i>
                </span>
                <span class="hide-menu">View Students</span>
              </a>
            </li> 
            
            <li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($payFees)) { echo $payFees; }?>" href="pay-fees.php" aria-expanded="false">
                <span>
                  <i class="ti ti-download"></i>
                </span>
                <span class="hide-menu">Pay Student Fees</span>
              </a>
            </li>
			
			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($createExpenditure)) { echo $createExpenditure; }?>" href="expenditure.php" aria-expanded="false">
                <span>
                  <i class="ti ti-upload"></i>
                </span>
                <span class="hide-menu">Expenditure</span>
              </a>
            </li>
		
			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($dateWise)) { echo $dateWise; }?>" href="datewise_reports.php" aria-expanded="false">
                <span>
                  <i class="ti ti-calendar"></i>
                </span>
                <span class="hide-menu">Datewise Report</span>
              </a>
            </li>

			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($studentWise)) { echo $studentWise; }?>" href="studentwise_reports.php" aria-expanded="false">
                <span>
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Studentwise Report</span>
              </a>
            </li>

			<li class="sidebar-item">
              <a class="sidebar-link <?php if(isset($monthWise)) { echo $monthWise; }?>" href="expenditure_reports.php" aria-expanded="false">
                <span>
                  <i class="ti ti-list"></i>
                </span>
                <span class="hide-menu">Expenditure Report</span>
              </a>
            </li>		

          </ul>
		  
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->