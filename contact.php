<?php
// Contact form for dealsboss.com. The mailbox is read from a one-line file OUTSIDE the document root,
// so no address is in the repo: create $HOME/dealsboss-mailbox.txt in cPanel's File Manager.
$TO   = trim((string)@file_get_contents(dirname(__DIR__) . '/dealsboss-mailbox.txt'));
$FROM = 'contact@dealsboss.com';

$sent = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg   = trim($_POST['message'] ?? '');
    $reply = trim($_POST['reply'] ?? '');
    if ($msg === '' || strlen($msg) > 4000) $error = 'Please write a message (up to 4000 characters).';
    elseif ($reply !== '' && !filter_var($reply, FILTER_VALIDATE_EMAIL)) $error = 'That reply address does not look right.';
    elseif (!filter_var($TO, FILTER_VALIDATE_EMAIL)) $error = 'The form is not switched on yet.';
    else {
        $body = "Message from dealsboss.com contact page\n\nReply to: " . ($reply ?: '(none given)') . "\n\n" . $msg . "\n";
        $head = "From: Deals Boss <$FROM>\r\n" . ($reply ? "Reply-To: $reply\r\n" : '') . "Content-Type: text/plain; charset=utf-8\r\n";
        if (@mail($TO, 'Deals Boss contact', $body, $head)) $sent = 'Sent. Thank you.';
        else $error = 'Sending failed. Please try again later.';
    }
}
$h = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact Deals Boss</title>
<meta name="description" content="Write to Deals Boss. Merchants, questions about a promotion, or anything else.">
<link rel="canonical" href="https://dealsboss.com/contact.php">
<link rel="stylesheet" href="pages.css">
<style>
textarea, input { font: inherit; width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #8886; border-radius: 8px; background: transparent; color: inherit; }
textarea { min-height: 160px; }
button { font: inherit; padding: 10px 18px; border-radius: 8px; border: 1px solid #8888; background: transparent; color: inherit; cursor: pointer; }
label { display: block; margin: 14px 0 4px; }
.ok { border-color: #4a4; } .err { border-color: #c44; }
</style>
</head>
<body>
<nav><a href="./">Deals Boss</a> <a href="privacy.html">Privacy</a> <a href="contact.php">Contact</a></nav>
<h1>Contact</h1>
<p>Merchants with an offer, a question about a promotion you saw, or anything else. A reply address is optional; without one we can read your message but not answer it.</p>
<?php if ($sent): ?><div class="box ok"><?= $h($sent) ?></div><?php endif; ?>
<?php if ($error): ?><div class="box err"><?= $h($error) ?></div><?php endif; ?>
<form method="post" action="contact.php">
  <label for="message">Message</label>
  <textarea id="message" name="message" required maxlength="4000"><?= $h($_POST['message'] ?? '') ?></textarea>
  <label for="reply">Reply address (optional)</label>
  <input id="reply" name="reply" type="email" autocomplete="email" value="<?= $h($_POST['reply'] ?? '') ?>">
  <p><button type="submit">Send</button></p>
</form>
<footer>What happens to a message is on the <a href="privacy.html">privacy page</a>.</footer>
</body>
</html>
