import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Dashboard = ({ setCurrentTab }) => {
    const [summary, setSummary] = useState({ leads_today: 0, bookings_today: 0, revenue_today: 0, sales_today: 0 });

    useEffect(() => {
        apiFetch({ path: '/cce/v1/analytics/summary' }).then(response => {
            if (response.success) {
                setSummary(response.data);
            }
        }).catch(err => console.error(err));
    }, []);

    return (
        <div>
            <div className="cce-card onboarding-guide" style={{marginBottom: '30px', borderLeft: '4px solid #0073aa'}}>
                <h2>🚀 Get Started: Your 3-Step Setup Guide</h2>
                <div style={{display: 'flex', gap: '20px'}}>
                    <div style={{flex: 1}}>
                        <strong>1. Create Your First Offer</strong>
                        <p>Define what you sell.</p>
                    </div>
                    <div style={{flex: 1}}>
                        <strong>2. Build Your Funnel</strong>
                        <p>Use pre-built templates.</p>
                    </div>
                    <div style={{flex: 1}}>
                        <strong>3. Connect Payments</strong>
                        <p>Link Stripe or PayPal.</p>
                    </div>
                </div>
            </div>

            <div className="cce-card-grid">
                <div className="cce-card">
                    <h3>Leads Today</h3>
                    <div className="value">{summary.leads_today}</div>
                </div>
                <div className="cce-card">
                    <h3>Bookings Today</h3>
                    <div className="value">{summary.bookings_today}</div>
                </div>
                <div className="cce-card">
                    <h3>Sales Today</h3>
                    <div className="value">{summary.sales_today}</div>
                </div>
                <div className="cce-card">
                    <h3>Revenue Today</h3>
                    <div className="value">${summary.revenue_today.toLocaleString()}</div>
                </div>
            </div>
            <div className="quick-actions" style={{marginTop: '20px'}}>
                <h2>Quick Actions</h2>
                <div style={{display: 'flex', gap: '10px'}}>
                    <button className="button button-primary button-hero" onClick={() => setCurrentTab('Funnels')}>Create Funnel</button>
                    <button className="button button-hero" onClick={() => setCurrentTab('Clients')}>Add Offer</button>
                    <button className="button button-hero" onClick={() => setCurrentTab('Funnels')}>Create Form</button>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
