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

// Dynamic Social Links Repeater
const socialWrapper = document.getElementById('axx-admin-socials-wrapper');

document.getElementById('axx-add-admin-social').addEventListener('click', function(e) {
    e.preventDefault();
    const index = socialWrapper.children.length;
    const row = document.createElement('div');
    row.className = 'axx-social-row';
    row.style.cssText = 'display:flex; gap:10px; margin-bottom:10px; align-items:center;';
    
    // Build options
    let optionsHtml = '<option value="">Select Platform...</option>';
    for (const [key, data] of Object.entries(window.axxSocialNetworks)) {
        optionsHtml += `<option value="${key}">${data.label}</option>`;
    }

    row.innerHTML = `
        <select name="socials[${index}][platform]" class="axx-social-select">
            ${optionsHtml}
        </select>
        <div class="axx-social-input-group" style="flex:1;">
            <span class="axx-social-addon">...</span>
            <input type="text" name="socials[${index}][handle]" placeholder="Select platform" />
        </div>
        <a href="#" class="axx-social-remove">&times; Remove</a>
    `;
    socialWrapper.appendChild(row);
});

// Handle Remove and Dynamic Prefix Changes
socialWrapper.addEventListener('change', function(e) {
    if(e.target.classList.contains('axx-social-select')) {
        const platform = e.target.value;
        const group = e.target.nextElementSibling; // the .axx-social-input-group div
        const addon = group.querySelector('.axx-social-addon');
        const input = group.querySelector('input');
        
        if (platform && window.axxSocialNetworks[platform]) {
            addon.textContent = window.axxSocialNetworks[platform].prefix;
            input.placeholder = window.axxSocialNetworks[platform].placeholder;
        } else {
            addon.textContent = '...';
            input.placeholder = '';
        }
    }
});

socialWrapper.addEventListener('click', function(e) {
    if(e.target.classList.contains('axx-social-remove')) {
        e.preventDefault();
        e.target.parentElement.remove();
    }
});