<?php
    require_once("./api/dashboardRequiredTasks.php");
    /**
     * @var string $accLabel1 The label for the first account
     * @var string $accLabel2 The label for the first account
     * @var string $accLabel3 The label for the first account
     * @var string $accountID1 The ID for the first account
     * @var string $accountID2 The ID for the first account
     * @var string $accountID3 The ID for the first account
     */
?>

<div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a href="home.php" class="simple-text logo-normal">
          <div class="logo-image-big">
            <img alt="Ultifree Hosting Logo" src="./assets/img/logo.png">
          </div>
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav" id="sidebarNav">
          <li>
            <a href="home.php">
              <i class="nc-icon nc-bank"></i>
              <p>All Accounts</p>
            </a>
          </li>
          <?php
            // Print the right number of nav options depending on number of accounts
            $accountDisplayNumberValue = 1; # For displaying the correct number of accounts. eg. acc1 is null then 2 should display as #1.

            if ($accountID1 != NULL) {
              echo "<li>";
                echo '<a href="hostAccount.php?acc=1&accDisp=' . $accountDisplayNumberValue .'">';
                  $shortenedLabel = substr($accLabel1, 0, min(20, strlen($accLabel1)));
                  echo "Account #$accountDisplayNumberValue - $shortenedLabel";
                echo '</a>';
              echo '</li>';
              $accountDisplayNumberValue++;
            }

            if ($accountID2 != NULL) {
              echo "<li>";
                echo '<a href="hostAccount.php?acc=2&accDisp=' . $accountDisplayNumberValue .'">';
                  $shortenedLabel = substr($accLabel2, 0, min(20, strlen($accLabel2)));
                  echo "Account #$accountDisplayNumberValue - $shortenedLabel";
                echo '</a>';
              echo '</li>';
              $accountDisplayNumberValue++;
            }

            if ($accountID3 != NULL) {
              echo "<li>";
                echo '<a href="hostAccount.php?acc=3&accDisp=' . $accountDisplayNumberValue .'">';
                  $shortenedLabel = substr($accLabel3, 0, min(20, strlen($accLabel3)));
                  echo "Account #$accountDisplayNumberValue - $shortenedLabel";
                echo '</a>';
              echo '</li>';
              $accountDisplayNumberValue++;
            }
          ?>
          <li>
            <a href="createAccountType.php">
              <i class="nc-icon nc-simple-add"></i>
              <p>Add Account</p>
            </a>
          </li>
          <hr />
          <li>
              <a href="ssl.php">
                  <i class="nc-icon nc-lock-circle-open"></i>
                  <p>SSL Certificates</p>
              </a>
          </li>
          <li>
            <a href="https://www.namesilo.com/?rid=c06f665gs" onclick="NameSiloEvent('Dashboard NavBar', 'General');">
              <i class="nc-icon nc-globe"></i>
              <p>Domains</p>
            </a>
          </li>
          <li>
            <a href="upgrade.php" onclick="PremiumEvent('Dashboard NavBar', 'Premium Link');">
              <i class="nc-icon nc-diamond"></i>
              <p>Upgrade to Premium</p>
            </a>
          </li>
          <hr />
          <li>
            <a href="https://affiliates.ultifreehosting.com">
              <i class="nc-icon nc-single-02"></i>
              <p>Affiliates</p>
            </a>
          </li>
          <li>
            <a href="https://ultifreehosting.com/faq.php">
              <i class="nc-icon nc-alert-circle-i"></i>
              <p>Knowledge Base</p>
            </a>
          </li>
          <hr />
          <li>
            <a href="https://www.trustpilot.com/review/ultifreehosting.com">
              <i class="nc-icon nc-paper"></i>
              <p>Rate us on TrustPilot</p>
            </a>
          </li>
          <li>
            <a href="https://g.page/ultifree-hosting/review?rc">
              <i class="nc-icon nc-paper"></i>
              <p>Review us on <span style="color: #4285F4;">G</span><span style="color: #EA4335;">o</span><span style="color: #FBBC05;">o</span><span style="color: #4285F4;">g</span><span style="color: #34A853;">l</span><span style="color: #EA4335;">e</span></p>
            </a>
          </li>
          <hr>
          <li class="text-center">
            <a href="https://ifastnet.com/portal/aff.php?aff=26864" target="_blank" onclick="iFastNetEvent('Dashboard NavBar', 'General');">Powered By iFastNet</a>
          </li>
          <li class="text-center text-muted copyright-header">
            Copyright © Ultifree Hosting
          </li>
        </ul>
      </div>
    </div>

<!-- Modal for Auto Logout -->
<div class="modal fade" id="autoLogoutModal" tabindex="-1" role="dialog" aria-labelledby="Auto Logout Warning" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autoLogoutModalTitle">Inactivity Warning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You will be logged out within the next minute! Interact with the website to continue using it.</p>
            </div>
        </div>
    </div>
</div>