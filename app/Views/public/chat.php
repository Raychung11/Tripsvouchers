<?php /** @var array $campaigns */ ?>
<div class="container-sm" style="max-width:680px; margin:0 auto;">
  <div class="flex-between mb-1">
    <h1 class="mt-0 mb-0"><?= e(__('chat.title')) ?> 🤖</h1>
  </div>
  <div class="chat-shell">
    <div class="chat-msgs" id="chat-msgs">
      <div class="chat-msg bot"><?= e(__('chat.welcome')) ?></div>
      <?php if (!empty($campaigns)): ?>
        <div class="chat-msg bot">
          🎁 <?= e(__('campaign.list_title')) ?>:
          <ul style="margin:6px 0 0; padding-left:1.2em;">
            <?php foreach (array_slice($campaigns, 0, 3) as $c): ?>
              <li><strong><?= e($c['campaign_name']) ?></strong> — <?= e(rm($c['voucher_value'])) ?>
                · <a href="<?= e(url('/claim/' . $c['id'])) ?>"><?= e(__('chat.voucher_cta')) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
    <form class="chat-input" id="chat-form">
      <?= csrf_field() ?>
      <input type="text" class="input" name="message" id="chat-input" placeholder="<?= e(__('chat.placeholder')) ?>" autocomplete="off" required>
      <button class="btn btn-primary"><?= e(__('chat.send')) ?></button>
    </form>
  </div>
</div>

<?php
$endpoint = url('/chat/message');
$voucher = __('chat.voucher_cta');
$loading = __('common.loading');
\App\Core\View::pushScript('<script>
(function(){
  const form = document.getElementById("chat-form");
  const input = document.getElementById("chat-input");
  const list = document.getElementById("chat-msgs");
  const csrf = form.querySelector(\'[name=_csrf]\').value;
  function add(text, who) {
    const d = document.createElement("div");
    d.className = "chat-msg " + who;
    d.textContent = text;
    list.appendChild(d);
    list.scrollTop = list.scrollHeight;
    return d;
  }
  function addCampaigns(cards) {
    if (!cards || !cards.length) return;
    const d = document.createElement("div");
    d.className = "chat-msg bot";
    const ul = document.createElement("ul");
    ul.style.margin = "6px 0 0"; ul.style.paddingLeft = "1.2em";
    cards.slice(0,3).forEach(c => {
      const li = document.createElement("li");
      li.innerHTML = "<strong></strong> — <span></span> · <a class=\"link\"></a>";
      li.querySelector("strong").textContent = c.name;
      li.querySelector("span").textContent = c.value;
      const a = li.querySelector("a");
      a.href = c.claim_url; a.textContent = "' . addslashes($voucher) . '";
      ul.appendChild(li);
    });
    d.appendChild(ul);
    list.appendChild(d);
    list.scrollTop = list.scrollHeight;
  }
  form.addEventListener("submit", async function(e){
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg) return;
    add(msg, "user");
    input.value = "";
    const loading = add("' . addslashes($loading) . '", "bot");
    try {
      const fd = new FormData();
      fd.append("_csrf", csrf);
      fd.append("message", msg);
      const res = await fetch("' . $endpoint . '", { method: "POST", body: fd });
      const data = await res.json();
      loading.remove();
      add(data.reply || "...", "bot");
      addCampaigns(data.campaigns);
    } catch (err) {
      loading.textContent = "Sorry, something went wrong.";
    }
  });
})();
</script>');
?>
