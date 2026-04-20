# 🌪 Strategic Funnel Templates: The Complete Setup & Customization Guide

The Funnel Engine is the heart of your high-ticket acquisition system. This guide will show you exactly how to deploy, customize, and optimize your funnels using our pre-built strategic templates.

---

## 🚀 1. How to Create and Manage Funnels

The Funnel Engine allows you to build multi-step conversion journeys. You have two ways to create them:

### A. One-Click Deployment (Recommended)
1.  **Navigate to Funnels:** Go to **Coach Engine > Funnels** in your WordPress sidebar.
2.  **Strategic Library:** Scroll down to the "Strategic Funnel Templates" section.
3.  **Deploy:** Click the **"Deploy"** button on any card (e.g., Consultation).
    - *The Engine will automatically create all the steps (Opt-in, Booking, etc.) with the correct strategic settings.*

### B. Building from Scratch
1.  **Duplicate:** Find an existing funnel and click **"Duplicate"** to start with a proven structure.
2.  **Add Steps:** Click **"View Steps"** on any funnel, then click the **"+ Add Step"** button.

---

## 🛠 2. Customizing Your Funnel Steps

"Where does the content come from?" is the most common question. The Coach Client Engine uses **Dynamic Rendering**. Instead of you writing HTML, the Engine renders professional, high-converting forms based on your settings.

### Understanding Step Types (The Dropdown)
When you add or edit a step, you choose a "Step Type." Here is what each one actually does:

1.  **Opt-in Form (optin):**
    *   **What's inside:** A professional name and email capture form.
    *   **Process:** Captured data is sent to the **Leads** database and triggers any "New Lead" **Automation Rules**.
    *   **How to Edit Content:** The form title is the **Step Title**. You can change it by clicking the gear icon (⚙).

2.  **Booking/Scheduling (booking):**
    *   **What's inside:** A dynamic calendar and qualification questionnaire.
    *   **Process:** Leads choose a time and answer your questions. Once confirmed, they are moved to the "Booked" stage in the **CRM**.
    *   **How to Edit Content:** The questions are managed in the **Bookings** tab. Change them there to update all funnels simultaneously.

3.  **Checkout/Payment (checkout):**
    *   **What's inside:** A frictionless high-ticket payment form for Stripe or PayPal.
    *   **Process:** Upon payment, the lead is moved to the "Closed" stage in the **CRM** and can access the **Client Portal**.
    *   **How to Edit Content:** Go to the **Clients** tab to edit the price, title, and "Dream Outcome" description of your offer.

4.  **Thank You Page (thank_you):**
    *   **What's inside:** A confirmation message OR a redirect to a VSL/Video.
    *   **How to Edit Content:** Click the gear icon (⚙) on the step. You can enter a **Success Message** or a **Redirect URL**.
    *   *Strategic Use:* In the "Hybrid Closer" funnel, the 2nd step (VSL Presentation) is a `thank_you` type that redirects to your video page.

---

## 🛠 2. Customizing Your Funnel Steps

Once deployed, your funnel appears in the "Your Funnels" table. Click **"View Steps"** to reveal the architecture.

### A. Editing Step Titles & Order
*   **Rename:** Click the gear icon (⚙) next to a step to change its internal title.
*   **Reorder:** Use the up (↑) and down (↓) arrows to move steps.
    *   *Example:* Move "VSL Video" before "Booking" to ensure prospects are pre-sold before they see your calendar.

### B. Configuring Step Logic
Click the gear icon (⚙) on a specific step to open the **Step Config Modal**.

#### 1. Opt-in Steps
*   **Goal:** Capture First Name and Email.
*   **Customization:** The form is automatically generated. To change where the user goes next, ensure the next step in the list is the one you want.
*   **Pro Tip:** Use the `redirect` attribute in the shortcode if you want to send them to an external URL instead of the next step.

#### 2. Booking Steps
*   **Goal:** Get a qualified lead on your calendar.
*   **Customization:** Go to the **Bookings** tab to add qualifying questions (e.g., "What is your current monthly revenue?"). These questions will automatically appear on the booking step of your funnel.

#### 3. Checkout Steps
*   **Goal:** Secure the high-ticket payment.
*   **Link to Offer:** In the Step Config, select which **Coaching Offer** (created in the Clients tab) this step should sell.
*   **Example:** If you're selling a "90-Day Accelerator," link this step to that specific offer so the correct price is displayed.

#### 4. Thank You / Success Steps
*   **Goal:** Confirm the action and provide next steps.
*   **Message:** Enter a custom success message (e.g., "Check your inbox for your login details!").
*   **External Redirect:** Instead of a message, you can provide a URL to redirect the user (e.g., to your private Facebook Group).

---

## 📝 3. Step-by-Step Execution: Building a Consultation Funnel

Follow these exact steps to launch your first high-ticket funnel today.

### Step 1: Define the Offer
Go to **Clients > Add Offer**. Create an offer titled "Discovery Strategy Session." Set the price to $0 (or your session fee).

### Step 2: Deploy the Template
Go to **Funnels**, find the "Consultation" card, and click **Deploy**.

### Step 3: Embed the Funnel
Create a new WordPress Page (Pages > Add New). Title it "Apply for Coaching." Paste the shortcode: `[cce_funnel id="1"]` (replace 1 with your actual Funnel ID).

### Step 4: Add Qualification
Go to **Bookings**. Add these three questions:
1.  "What is your #1 business goal for the next 12 months?" (Textarea)
2.  "Are you able to invest $3k+ into your growth if this is a fit?" (Text)
3.  "How soon are you looking to start?" (Text)

### Step 5: Test the Journey
Open your new page in an Incognito window. Opt-in, book a test slot, and ensure you are moved to the "Booked" stage in the **CRM**.

---

## 💡 4. Advanced Optimization Tips

*   **Conversion Tracking:** In the "View Steps" area, look at the **visits vs. conversions** for each step. If your "Opt-in Page" has 100 visits but 0 conversions, your headline is the problem.
*   **Email Automation:** Link a "New Lead" trigger in the **Automation** tab to a 3-day indoctrination sequence.
*   **The "Secret" Shortcode:** You don't have to use the full funnel shortcode. You can embed JUST the booking form on a custom page using `[cce_booking title="Schedule Your Audit"]`.
