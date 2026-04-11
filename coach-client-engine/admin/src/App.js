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
                {currentTab !== 'Dashboard' && <p>This module is coming soon in the production version.</p>}
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

export default App;
