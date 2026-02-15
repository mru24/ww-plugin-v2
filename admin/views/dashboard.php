<?php
/**
 * Admin View: Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
  <h1>Dashboard</h1>

  <hr>

  <div class="dashboard-widgets">
    <div class="dashboard-row">
      <div class="stat-card">
        <div class="stat-number"><?php echo esc_html($subscriptions_count); ?></div>
        <div class="stat-label">Total Subscriptions</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo esc_html($posts_count); ?></div>
        <div class="stat-label">Total Posts</div>
      </div>
      <div class="stat-card">

      </div>
      <div class="stat-card">

      </div>
      <div class="stat-card">

      </div>
      <div class="stat-card">

      </div>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-main">
        <!-- Left Column -->
        <div class="dashboard-column">


        </div>

        <!-- Right Column -->
        <div class="dashboard-column">

        </div>
    </div>
  </div>
</div>

<style>
.dashboard-widgets { margin: 20px 0; }
.dashboard-row { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
.dashboard-main { display: flex; gap: 20px; }
.dashboard-column { flex: 1; display: flex; flex-direction: column; gap: 20px; }
.dashboard-widget { background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; }
.widget-header { padding: 15px 20px; border-bottom: 1px solid #ccd0d4; display: flex; justify-content: space-between; align-items: center; }
.widget-header h3 { margin: 0; }
.widget-content { padding: 20px; }

.stat-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    min-width: 150px;
    flex: 1;
    text-align: center;
}
.stat-number { font-size: 2em; font-weight: bold; color: #2271b1; line-height: 1; }
.stat-label { color: #666; margin-top: 5px; font-size: 0.9em; }

.utilization-bar-container {
    position: relative;
    background: #f0f0f1;
    height: 24px;
    border-radius: 12px;
    overflow: hidden;
}
.utilization-bar {
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 12px;
}
.utilization-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8em;
    font-weight: bold;
    color: #000;
    text-shadow: 1px 1px 0 #fff;
}

.quick-actions { display: flex; flex-direction: column; gap: 10px; }
.quick-actions .button { text-align: center; }

.status-breakdown { display: flex; flex-direction: column; gap: 10px; }
.status-item { display: flex; align-items: center; gap: 10px; }
.status-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
.status-booked { background-color: #65a2d3; }
.status-confirmed { background-color: #19dd16; }
.status-draft { background-color: #fff3cd; }
.status-cancelled { background-color: #f8d7da; }
.status-label { flex: 1; }
.status-count { font-weight: bold; }

.system-info { display: flex; flex-direction: column; gap: 8px; }
.info-item { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f0f0f1; }
.info-item:last-child { border-bottom: none; }
.info-label { font-weight: 500; }
.info-value { color: #666; }

.booking-status {
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    font-size: 0.85em;
}
.status-booked { background-color: #65a2d3; color: #d7e7f4; }
.status-confirmed { background-color: #19dd16; color: #d7e7f4; }
.status-draft { background-color: #fff3cd; color: #856404; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }

@media (max-width: 1200px) {
    .dashboard-main { flex-direction: column; }
}
</style>