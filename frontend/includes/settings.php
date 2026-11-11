<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Messages for the codes set by regen-token.php and password-updater.php
$settingsErrors = [
    'db'       => 'Failed to connect to the database',
    'fields'   => 'Please fill in all the required fields',
    'mismatch' => 'New passwords do not match',
    'short'    => 'New password must be at least 8 characters',
    'current'  => 'Current password is incorrect!',
    'account'  => 'Your user account does not exist',
    'token'    => 'The API token could not be regenerated. Please rebuild the database!',
    'csrf'     => 'Your session expired, please try again',
];
$settingsError   = $settingsErrors[get_str('err')] ?? '';
$tokenRegenerated = get_str('ok') === 'token';

$apiToken = 'Non existent. Please rebuild the database!';
$conn = db_connect();
if ($conn === null) {
    echo "<p class='notice'>Connection to the database failed</p>";
    return;
}
$result = $conn->query('SELECT api_key FROM `api_keys` LIMIT 1');
if ($result && ($row = $result->fetch_assoc())) {
    $apiToken = $row['api_key'];
}
$conn->close();
?>

<div class="settings">
    <div class="manageToken">
        <h1 id="settingsH1">Manage API Token</h1>
        <h2 id="settingsH2">Current token: <input type="text" id="apiToken" value="<?= e($apiToken) ?>" readonly></h2>
        <?php if ($tokenRegenerated): ?>
            <p class="success">New token created. Remember to update it on your sensor stations.</p>
        <?php endif; ?>
        <form action="./includes/regen-token.php" method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
            <input type="hidden" name="token" value="<?= e($apiToken) ?>" />
            <input type="submit" value="Regenerate" />
        </form>
    </div>

    <form id="passwordUpdateForm" action="./includes/password-updater.php" method="post">
        <h1 id="settingsH1">Change password</h1>
        <h2 id="settingsErrorH2"><?= e($settingsError) ?></h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <input
            type="password"
            name="passwordCurrent"
            placeholder="Current Password"
            id="passwordCurrent"
            autocomplete="current-password"
            required
        />
        <input
            type="password"
            name="password"
            placeholder="Password"
            id="password"
            autocomplete="new-password"
            required
        />
        <input
            type="password"
            name="passwordConfirm"
            placeholder="Confirm Password"
            id="passwordConfirm"
            autocomplete="new-password"
            required
        />
        <input type="submit" value="Update" />
    </form>
</div>
