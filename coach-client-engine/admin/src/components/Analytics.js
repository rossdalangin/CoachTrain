import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Analytics = () => {
    const [summary, setSummary] = useState({ total_visitors: 0, leads_today: 0, bookings_today: 0 });

    useEffect(() => {
        apiFetch({ path: '/cce/v1/analytics/summary' }).then(res => {
            if (res.success) setSummary(res.data);
        });
    }, []);

    return (
        <div className="cce-card">
            <h3>SaaS Business Intelligence</h3>
            <div className="cce-card-grid" style={{marginBottom: '30px'}}>
                <div className="cce-card" style={{background: '#f0f4f8'}}>
                    <h3>Total Visitors</h3>
                    <div className="value">{summary.total_visitors}</div>
                </div>
                <div className="cce-card" style={{background: '#f0f4f8'}}>
                    <h3>Lead Conversion</h3>
                    <div className="value">{summary.total_visitors > 0 ? ((summary.leads_today / summary.total_visitors) * 100).toFixed(1) : 0}%</div>
                </div>
            </div>

            <div style={{background: '#f8f9fa', padding: '20px', borderRadius: '8px', borderLeft: '4px solid #0073aa'}}>
                <h4>💡 Recommended Actions:</h4>
                <ul>
                    <li>Your visitor-to-lead conversion is low. <strong>Add a popup</strong> to your homepage to capture more traffic.</li>
                    <li>Leads are dropping off at the questionnaire. <strong>Reduce the number of questions</strong> to 3–5.</li>
                </ul>
            </div>
        </div>
    );
};

export default Analytics;
