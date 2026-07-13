document.getElementById('axx-add-admin-award').addEventListener('click', function(e) {
    e.preventDefault();
    const wrapper = document.getElementById('axx-admin-awards-wrapper');
    const index = wrapper.children.length;
    const row = document.createElement('div');
    row.className = 'axx-award-row';
    row.innerHTML = `
        <input type="text" name="awards[${index}][title]" placeholder="Award Title" />
        <input type="text" name="awards[${index}][place]" placeholder="Place" />
        <input type="text" name="awards[${index}][image]" placeholder="Image URL (Optional)" />
        <a href="#" class="axx-award-remove">&times; Remove</a>
    `;
    wrapper.appendChild(row);
});

document.getElementById('axx-admin-awards-wrapper').addEventListener('click', function(e) {
    if(e.target.classList.contains('axx-award-remove')) {
        e.preventDefault();
        e.target.parentElement.remove();
    }
});

// Admin Trigger for "Lost Key"
document.getElementById('axx-admin-trigger-reset').addEventListener('click', function(e) {
    e.preventDefault();
    const btn = this;
    btn.disabled = true;
    jQuery.post(ajaxurl, {
        action: 'axx_market_admin_trigger_reset',
        maker_id: btn.getAttribute('data-maker'),
        nonce: '<?php echo wp_create_nonce("axx_market_admin_nonce"); ?>'
    }, function(res) {
        if(res.success) {
            document.getElementById('axx-reset-msg').style.display = 'inline';
            setTimeout(() => location.reload(), 1500); // Reload to show blank date
        }
    });
});