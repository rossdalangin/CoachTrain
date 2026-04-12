# Coach Client Engine

All-in-one client acquisition system specifically built for coaches and consultants.

## Core Features
- **Leads Module:** Capture and manage leads.
- **Bookings Module:** Schedule sessions with built-in questionnaire.
- **CRM Module:** Pipeline management with Kanban view.
- **Funnels Builder:** Step-based funnel creation.
- **Payments:** Stripe and PayPal integration for offers.
- **Analytics:** Conversion and revenue tracking.

## Installation
1. Upload the `coach-client-engine` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the dashboard from the "Coach Engine" menu in the admin sidebar.

## Development
This plugin uses React for the admin interface.
To build the assets:
```bash
cd coach-client-engine
npm install
npm run build
```

## Licensing
This plugin includes a freemium model. Use a license key starting with `PRO-` to unlock professional features.

## 🚀 Consultant Strategy Guide (The "Hormozi/Brunson" Method)
To get 3–5 high-paying clients monthly with this engine:
1.  **The Grand Slam Offer:** Use the **Clients** tab to create an offer that is "so good they feel stupid saying no." Focus on outcomes, not hours.
2.  **The Consultation Funnel:** Deploy a funnel using the **Funnels** tab.
    - Step 1: Add value with a Lead Magnet.
    - Step 2: Immediate invitation to book a "Strategy Session" (Consultation).
    - Step 3: Use the **Pre-call Questionnaire** to qualify leads.
3.  **Speed to Lead:** Monitor your **CRM Kanban**. When a 'HOT' lead comes in, use the **Quick Contact** action to reach out within 5 minutes.
4.  **Omnipresent Proof:** Embed the `[cce_testimonials]` shortcode on every page. High-ticket sales are built on trust.
5.  **Analytics Mastery:** If your "Conversion Rate" is below 5%, refine your offer description or questionnaire.
