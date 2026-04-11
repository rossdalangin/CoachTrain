import React, { useState } from 'react';

const App = () => {
    const [currentTab, setCurrentTab] = useState('Dashboard');

    const tabs = [
        'Dashboard', 'Leads', 'Bookings', 'Clients', 'Funnels',
        'Automation', 'CRM', 'Client Portal', 'Proof', 'Analytics', 'Settings'
    ];

    return (
        <div className="cce-admin-wrapper">
            <aside className="cce-sidebar">
                <ul>
                    {tabs.map(tab => (
                        <li
                            key={tab}
                            className={currentTab === tab ? 'active' : ''}
                            onClick={() => setCurrentTab(tab)}
                        >
                            {tab}
                        </li>
                    ))}
                </ul>
            </aside>
            <main className="cce-main-content">
                <h1>{currentTab}</h1>
                {currentTab === 'Dashboard' && <DashboardView />}
                {currentTab === 'Automation' && <AutomationView />}
                {currentTab === 'Client Portal' && <PortalView />}
                {currentTab === 'Proof' && <ProofView />}
                {!['Dashboard', 'Automation', 'Client Portal', 'Proof'].includes(currentTab) &&
                    <p>This module is coming soon in the production version.</p>
                }
            </main>
        </div>
    );
};

const DashboardView = () => (
    <div>
        <div className="cce-card-grid">
            <div className="cce-card">
                <h3>Leads today</h3>
                <div className="value">12</div>
            </div>
            <div className="cce-card">
                <h3>Bookings today</h3>
                <div className="value">3</div>
            </div>
            <div className="cce-card">
                <h3>Sales today</h3>
                <div className="value">2</div>
            </div>
            <div className="cce-card">
                <h3>Revenue</h3>
                <div className="value">$1,500</div>
            </div>
        </div>
        <div className="quick-actions">
            <h2>Quick Actions</h2>
            <button className="button button-primary">Create Funnel</button>
            <button className="button" style={{marginLeft: '10px'}}>Add Offer</button>
            <button className="button" style={{marginLeft: '10px'}}>Create Form</button>
        </div>
    </div>
);

const AutomationView = () => (
    <div className="cce-card">
        <h3>Active Workflows</h3>
        <ul>
            <li>On Opt-in → Send Welcome Email</li>
            <li>On Booking → Send Reminder (24h before)</li>
        </ul>
        <button className="button">Create Workflow</button>
    </div>
);

const PortalView = () => (
    <div className="cce-card">
        <h3>Shared Resources</h3>
        <table className="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Welcome Pack</td>
                    <td>PDF</td>
                    <td>Edit</td>
                </tr>
            </tbody>
        </table>
        <button className="button" style={{marginTop: '10px'}}>Upload Resource</button>
    </div>
);

const ProofView = () => (
    <div className="cce-card">
        <h3>Client Testimonials</h3>
        <div className="cce-card-grid">
            <div className="cce-card" style={{border: '1px solid #ddd'}}>
                <p>"Best coaching ever!"</p>
                <strong>- John Doe</strong>
            </div>
        </div>
        <button className="button">Add Testimonial</button>
    </div>
);

export default App;
