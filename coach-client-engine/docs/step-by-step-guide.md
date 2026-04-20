# Coach Client Engine: The Master Implementation Blueprint

Welcome to the inner circle. This guide will take you from a fresh install to a 7-figure acquisition system in 6 simple phases.

---

## 🛠 Phase 1: Foundations (The First 15 Minutes)
1. **The Plugin Handshake:** Install and activate. Your new command center is in the "Coach Engine" sidebar menu.
2. **Identity Setup:** Navigate to **Settings > General**.
   - **Coach Name:** This appears in your emails and portal. *Example: "John Doe Coaching".*
   - **Default Currency:** Choose the currency you bill in. *Example: "USD ($)".*
3. **The Gateway:** Go to **Settings > Payments**.
   - **Stripe:** Enter your Secret Key (starts with `sk_`).
   - **Test Mode:** Check this box to simulate payments without a real card. *Highly recommended during setup.*
4. **License Unlock:** Enter your PRO license key (starts with `PRO-`). This activates 1-click strategy deployment and advanced lead scoring.

## 🌪 Phase 2: The Funnel Engine (Building the Bridge)
1. **Choose Your Weapon:** Go to **Funnels > Create from Template**.
   - **Lead Magnet:** Trade a PDF for an email. *Example: "The 7-Figure Script Vault".*
   - **Consultation:** The most powerful funnel for coaches. Application -> Booking -> Success.
   - **Hybrid Closer:** For those who want to sell via VSL and allow immediate checkout.
2. **Deploy & Embed:** Click "Deploy." Copy the shortcode `[cce_funnel id="1"]`.
   - Create a page titled "Work With Me" and paste the code.
3. **Step Config:** Click "View Steps" and then the gear icon (⚙) on the "Checkout" step. Select your offer from the dropdown.

## 💎 Phase 3: The Offer Builder (The Math of Value)
1. **Craft the "Grand Slam":** Go to **Clients > Add Offer**.
2. **Hormozi Variables:** High-ticket coaching isn't about time; it's about transformation.
   - **Dream Outcome:** *Example: "Add $100k in ARR to your agency in 6 months."*
   - **Perceived Likelihood:** *Example: "Join 50+ other agencies using our proven LinkedIn method."*
   - **Time Delay:** *Example: "Book your first 3 calls in the first 7 days."*
   - **Effort & Sacrifice:** *Example: "We handle all the technical CRM setup for you."*
3. **The Value Equation:** Focus on maximizing the top side (Outcome/Likelihood) and minimizing the bottom side (Time/Effort).
4. **Implementation:** Link your offer to a "Checkout" step in your funnel to collect payments instantly.

## ⚙️ Phase 4: Automation (The Silent Employee)
1. **The Welcome Sequence:** Go to **Automation > Templates**. Create a "Welcome Guide" email. Then go to **Rules** and link `Trigger: New Lead` → `Action: Send Email`.
2. **The CRM Accelerator:** Link `Trigger: Booking Confirmed` → `Action: Move Stage (Booked)`. This keeps your Pipeline organized without manual work.
3. **SOP Automation:** Link `Trigger: Payment Received` → `Action: Create Task (Send Onboarding Kit)`. This ensures you never miss a critical client step.
4. **Qualifying Logic:** In the **Bookings** tab, add a question: *"What is your current monthly revenue?"*. If they answer below your threshold, you can manually cancel the call from the Bookings table.

## 🔒 Phase 5: Client Portal (The 5-Star Experience)
1. **The Private Hub:** Create a page on your site and add the `[cce_client_portal]` shortcode. This is where your clients will "live."
2. **Resource Seeding:** Go to **Portal > Resources**.
   - **Internal Resources:** Upload PDFs, scripts, or video links. *Example: "Sales Script Template".*
   - **Visibility:** Set to "Clients Only" to ensure only those who have paid can access them.
3. **Roadmap Builder:** Add onboarding tasks. *Example: "Schedule Your Kickoff Call", "Complete the Intake Form".* This gamifies the onboarding process for your clients.

## 📊 Phase 6: Analytics & Scaling (The CEO View)
1. **The Pulse Check:** Every Monday, check your **Analytics** dashboard.
2. **Strategy Insights:** Pay attention to the automated advice.
   - *If Show-up rate is low:* The Engine will suggest adding more SMS/Email reminders.
   - *If Conversion is low:* The Engine will point you to the Hormozi framework in the Hub.
3. **Revenue Projections:** Use the "Next 30 Days" projection to plan your ad spend or outreach volume. *If your projected revenue is $5k but you want $10k, you need to double your leads.*

---

## 🧠 Master Consultant FAQ
- **Multi-Tenancy:** Yes, if you have multiple coaches on one WP site, each user sees *only* their own leads, funnels, and settings.
- **Shortcode Power:** You can mix and match. Use `[cce_lead_capture]` on a blog post and `[cce_checkout]` on a custom sales page.
- **Support:** Access the "Mastery Hub" for outreach scripts and VSL frameworks designed by the pros.
