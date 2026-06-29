document.addEventListener('DOMContentLoaded', function() {
    const { createApp, ref } = Vue;

    if (!document.getElementById('axx-market-vue-mapper-root')) return;

    createApp({
        setup() {
            // Load data localized from PHP
            const categories = ref(window.axxMrkMappingData.categories || []);
            const ajaxUrl = window.axxMrkMappingData.ajaxUrl;
            const nonce = window.axxMrkMappingData.nonce;
            
            const isSaving = ref(false);
            const isSyncing = ref(false);
            const statusMessage = ref('');

            // Helper to recursively flatten the category tree for the UI
            const flattenCategories = (cats, depth = 0) => {
                let flat = [];
                cats.forEach(cat => {
                    flat.push({ ...cat, depth });
                    if (cat.children && cat.children.length > 0) {
                        flat = flat.concat(flattenCategories(cat.children, depth + 1));
                    }
                });
                return flat;
            };

            const flatCategories = ref(flattenCategories(categories.value));
            const newTagInputs = ref({});

            const addTag = (category) => {
                const inputVal = newTagInputs.value[category.id];
                if (inputVal && inputVal.trim() !== '') {
                    const tag = inputVal.trim().toLowerCase();
                    if (!category.mappedTags.includes(tag)) {
                        category.mappedTags.push(tag);
                    }
                    newTagInputs.value[category.id] = ''; // Clear input
                }
            };

            const removeTag = (category, index) => {
                category.mappedTags.splice(index, 1);
            };

            const saveMappings = () => {
                isSaving.value = true;
                statusMessage.value = 'Saving mappings...';

                // Format data for PHP: { category_id: ['tag1', 'tag2'] }
                const payload = {};
                flatCategories.value.forEach(cat => {
                    if (cat.mappedTags && cat.mappedTags.length > 0) {
                        payload[cat.id] = cat.mappedTags;
                    }
                });

                jQuery.post(ajaxUrl, {
                    action: 'axx_market_save_mappings',
                    nonce: nonce,
                    mappings: JSON.stringify(payload)
                }, function(response) {
                    isSaving.value = false;
                    if (response.success) {
                        statusMessage.value = '✅ ' + response.data;
                        setTimeout(() => statusMessage.value = '', 3000);
                    } else {
                        statusMessage.value = '❌ Error: ' + response.data;
                    }
                });
            };

            const runRetroactiveSync = () => {
                if(!confirm('This will scan all existing scraped inventory and update their WooCommerce categories. Proceed?')) return;
                
                isSyncing.value = true;
                statusMessage.value = 'Running retroactive sync. Do not close this window...';

                jQuery.post(ajaxUrl, {
                    action: 'axx_market_run_retroactive_sync',
                    nonce: nonce
                }, function(response) {
                    isSyncing.value = false;
                    if (response.success) {
                        statusMessage.value = '✅ ' + response.data;
                    } else {
                        statusMessage.value = '❌ Error: ' + response.data;
                    }
                });
            };

            return {
                flatCategories,
                newTagInputs,
                addTag,
                removeTag,
                saveMappings,
                runRetroactiveSync,
                isSaving,
                isSyncing,
                statusMessage
            };
        },
        template: `
            <div>
                <div class="category-list">
                    <div v-for="cat in flatCategories" :key="cat.id" class="category-item">
                        <div class="category-row">
                            <div class="category-info" :style="{ paddingLeft: (cat.depth * 20) + 'px' }">
                                <div class="category-name">{{ cat.name }}</div>
                                <div v-if="cat.breadcrumb" class="category-breadcrumb">{{ cat.breadcrumb }}</div>
                            </div>
                            <div class="category-tags">
                                <div class="tag-pills-container">
                                    <span v-for="(tag, index) in cat.mappedTags" :key="index" class="tag-pill">
                                        {{ tag }}
                                        <button @click="removeTag(cat, index)" class="tag-pill-remove" type="button" title="Remove">&times;</button>
                                    </span>
                                    <div class="tag-input-wrapper">
                                        <input type="text" 
                                               v-model="newTagInputs[cat.id]" 
                                               @keydown.enter.prevent="addTag(cat)"
                                               placeholder="Type tag & hit Enter..." 
                                               class="tag-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky-footer" style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                    <div style="font-weight:bold;">
                        <span v-if="statusMessage">{{ statusMessage }}</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button @click="runRetroactiveSync" :disabled="isSyncing || isSaving" class="button button-secondary">
                            {{ isSyncing ? 'Syncing...' : 'Run Now (Retroactive Sync)' }}
                        </button>
                        <button @click="saveMappings" :disabled="isSaving || isSyncing" class="button button-primary">
                            {{ isSaving ? 'Saving...' : 'Save Mappings' }}
                        </button>
                    </div>
                </div>
            </div>
        `
    }).mount('#axx-market-vue-mapper-root');
});