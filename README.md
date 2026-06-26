# Axxanoid Marketplace

**Contributors:** Axxanoid Studios LLC  
**License:** GPL-2.0-or-later  

An automated B2B Indie Marketplace and "Ego Trap" engine. This system uses headless Python drones to scrape indie makers from Etsy, auto-generates WooCommerce Affiliate products, and uses local AI (Qwen 14B) to pitch the makers a "Marketplace Rent" subscription to keep their products active on the Average Stoner domain.

## The Workflows

### 1. The Recon & Ingestion Flow (Python + Etsy)
* Python targets specific Etsy searches (e.g., "custom glass pipe").
* It extracts the Maker's shop details, email, and up to 10 top products (images, prices, outbound Etsy links). *Note: If no email is found, the target is immediately dropped.*
* **The Push:** Python pushes the products to the WooCommerce REST API as `external` products. 
* **The Link:** Python pushes the Maker to the custom WP REST API, generating an `axx_market_maker` profile. The system natively links this profile to a corresponding WooCommerce "Brand" taxonomy and sets their `trial_expiration_date` to **+10 Days**.

### 2. The Pitch & Trap Pipeline (Python + Qwen 14B)
* **Day 1 (The Hook):** Python feeds the Maker's name and top product to a local Qwen 14B model to generate a highly personalized, non-spammy email hook. The email alerts the Maker that their work is currently being featured to the 4,000+ member Average Stoner community.
* **Day 5 (The Follow-up):** Python queries WP for makers who have not upgraded. It sends a second email showing them their accumulated traffic and offers to lock in their spot permanently for just "$2.99 for 30 days" (pennies a day).

### 3. The B2B Customer Flow (The Maker)
* The Maker clicks the link in the email and lands on their Vanity Profile (which queries all WooCommerce products assigned to their specific Brand ID).
* **The Conversion:** A scarcity banner warns them of their trial expiration. Clicking "Claim Profile" drops them into a pristine WooCommerce checkout to purchase the manual $2.99 subscription. (We use manual billing, not auto-charge, to eliminate B2B conversion friction).
* **The Concierge:** Active, paying Makers unlock a "Request Profile Update" form on their page to submit priority product swaps or bio changes directly to the site admins.

### 4. The Executioner & Affiliate SEO Harvest (The Drop-off)
If a Maker does not pay their $2.99 rent by Day 10, their profile is dismantled, but their SEO value is harvested.
* **Day 10 (The Executioner):** A Python drone changes the Maker CPT to `draft` (unpublishing their vanity page). It then reassigns all of their WooCommerce products to a generic "Affiliate Finds" Brand, allowing Average Stoner to keep the inventory and SEO weight.
* **Day 45 (The Pruner):** Unpaid products sitting in the Affiliate brand hit their 45-day lifespan and are drafted/trashed to keep the catalog fresh and prevent dead links.

### 5. The Admin Flow
* **The Mapper:** Admins map Etsy keywords to native WooCommerce categories via a Vue.js interface. A "Run Now" feature allows admins to retroactively apply new mapping rules to all existing "Indie Finds" inventory.
* **The Concierge CRM:** Admin dashboard tracks Maker subscriptions and provides a ticketing interface to manage profile update requests.

## Technical Standards
* Strict separation of concerns: No inline CSS or HTML in PHP controllers. All layouts utilize template partials (`/public/templates/`) and enqueued stylesheets (`/public/assets/css/`).