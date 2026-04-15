# Coach Client Engine: Installation & Scaling Guide

# Coach Client Engine: Installation & Scaling Guide

## 🛠 1. Quick Installation & Multi-Tenant Setup
1. **Upload:** Upload the `coach-client-engine` folder to your `/wp-content/plugins/` directory.
2. **Activate:** Activate through the 'Plugins' menu in WordPress.
3. **Identity:** Go to **Coach Engine > Settings** and set your **Coach Name** and **Default Currency**. In a multi-tenant environment, these settings are unique to your user account.
4. **License:** Enter your license key (starts with `PRO-` for full automation features).

## 🚀 2. Setting Up Your First Funnel
1. Go to **Coach Engine > Funnels**.
2. Click **"Create from Template"**.
3. Select **"Consultation Funnel"**. This is the best for high-ticket coaching.
4. Customize your steps. Use the `[cce_funnel id="X"]` shortcode to display it on any page.

## 3. Creating Your "Grand Slam" Offer
1. Go to **Coach Engine > Clients**.
2. Create a new Offer.
3. Use the **Value Equation** fields to refine your pitch:
    - **Dream Outcome:** What is the ultimate goal for your client?
    - **Perceived Likelihood:** Why will they believe they can achieve it with you?
    - **Time Delay:** How fast can they see a win?
    - **Effort & Sacrifice:** How much "work" can you remove for them?

## ⚙️ 4. Automation Power-Ups
Automation is where you win back your time.
1. **The Sequence:** Go to **Coach Engine > Automation**.
2. **Create Rule:** Link `Trigger: New Lead Captured` to `Action: Send Email (Welcome Template)`.
3. **CRM Integration:** Link `Trigger: Booking Confirmed` to `Action: Move Stage (Booked)`.
4. **Task Delegation:** Link `Trigger: Payment Received` to `Action: Create Task (Onboard Client)`.

### 🛡 Webhook Hardening
For high-ticket payments via Stripe/PayPal:
- The Engine automatically handles `payment_intent.succeeded` events.
- Ensure your Webhook Secret in **Settings > Payments** matches your Stripe Dashboard for secure processing.

## 5. Shortcodes Reference
- `[cce_lead_capture]` - Lead magnet form.
- `[cce_booking]` - Scheduling calendar.
- `[cce_testimonials]` - Social proof wall.
- `[cce_checkout]` - Payment processing.
- `[cce_client_portal]` - Dedicated dashboard for active clients.
- `[cce_funnel id="123"]` - Embed a specific funnel.

## 6. Pro Tips for Consultants
- **Lead Scoring:** Check your CRM Pipeline regularly. Leads with high "Activity" are your hottest prospects.
- **Task Management:** Use the "Tasks" tab in a Lead's profile to never miss a follow-up.
- **Conversion Tracking:** Check **Coach Engine > Analytics** every Monday to see which funnel is performing best.
