import React, { useState, useEffect } from 'react';

const App = () => {
    const [currentTab, setCurrentTab] = useState('Dashboard');

    const tabs = [
        'Dashboard', 'Leads', 'Bookings', 'Clients', 'Funnels',
        'Automation', 'CRM', 'Client Portal', 'Proof', 'Analytics', 'Settings'
    ];

    return (
        <div className="cce-admin-wrapper">
            <aside className="cce-sidebar">
                <div className="cce-logo" style={{padding: '20px', fontWeight: 'bold', fontSize: '18px', borderBottom: '1px solid #333'}}>
                    Coach Client Engine
                </div>
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
                <header style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px'}}>
                    <h1 style={{margin: 0}}>{currentTab}</h1>
                    <div className="user-info">
                        {window.cceData?.isPro ? <span className="badge-pro" style={{background: '#ffc107', color: '#000', padding: '4px 8px', borderRadius: '4px', fontSize: '12px', fontWeight: 'bold'}}>PRO</span> : <button className="button button-primary">Upgrade to Pro</button>}
                    </div>
                </header>

                {currentTab === 'Dashboard' && <DashboardView />}
                {currentTab === 'Leads' && <LeadsView />}
                {currentTab === 'CRM' && <CRMView />}
                {currentTab === 'Automation' && <AutomationView />}
                {currentTab === 'Client Portal' && <PortalView />}
                {currentTab === 'Proof' && <ProofView />}
                {currentTab === 'Settings' && <SettingsView />}

                {!['Dashboard', 'Leads', 'CRM', 'Automation', 'Client Portal', 'Proof', 'Settings'].includes(currentTab) &&
                    <div className="cce-card">
                        <p>The <strong>{currentTab}</strong> module is currently in development and will be available in the next update.</p>
                        <button className="button">Join the Beta</button>
                    </div>
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
        <div className="quick-actions" style={{marginTop: '20px'}}>
            <h2>Quick Actions</h2>
            <div style={{display: 'flex', gap: '10px'}}>
                <button className="button button-primary button-hero">Create Funnel</button>
                <button className="button button-hero">Add Offer</button>
                <button className="button button-hero">Create Form</button>
            </div>
        </div>
    </div>
);

const LeadsView = () => {
    const [leads, setLeads] = useState([
        { id: 1, first_name: 'John', last_name: 'Doe', email: 'john@example.com', status: 'hot', created_at: '2023-10-27' },
        { id: 2, first_name: 'Jane', last_name: 'Smith', email: 'jane@example.com', status: 'warm', created_at: '2023-10-26' },
    ]);

    return (
        <div className="cce-card">
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    {leads.map(lead => (
                        <tr key={lead.id}>
                            <td>{lead.first_name} {lead.last_name}</td>
                            <td>{lead.email}</td>
                            <td><span className={`status-tag status-${lead.status}`}>{lead.status.toUpperCase()}</span></td>
                            <td>{lead.created_at}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

const CRMView = () => {
    const stages = ['New', 'Contacted', 'Booked', 'Closed'];
    return (
        <div className="cce-kanban" style={{display: 'flex', gap: '20px', overflowX: 'auto', paddingBottom: '20px'}}>
            {stages.map(stage => (
                <div key={stage} className="kanban-column" style={{minWidth: '250px', background: '#e2e8f0', borderRadius: '8px', padding: '15px'}}>
                    <h3 style={{marginTop: 0, fontSize: '16px', color: '#4a5568'}}>{stage}</h3>
                    <div className="kanban-cards">
                        {stage === 'New' && (
                            <div className="cce-card" style={{marginBottom: '10px', fontSize: '14px'}}>
                                <strong>John Doe</strong>
                                <div style={{fontSize: '12px', color: '#718096'}}>Hot Lead</div>
                            </div>
                        )}
                        <button className="button button-small" style={{width: '100%', borderStyle: 'dashed'}}>+ Add Lead</button>
                    </div>
                </div>
            ))}
        </div>
    );
};

const AutomationView = () => (
    <div className="cce-card">
        <h3>Active Workflows</h3>
        <table className="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Trigger</th>
                    <th>Action</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>On Opt-in</td>
                    <td>Send Welcome Email</td>
                    <td>Active</td>
                </tr>
                <tr>
                    <td>On Booking</td>
                    <td>Send Reminder (24h)</td>
                    <td>Active</td>
                </tr>
            </tbody>
        </table>
        <button className="button button-primary" style={{marginTop: '20px'}}>Create New Workflow</button>
    </div>
);

const PortalView = () => (
    <div className="cce-card">
        <h3>Shared Coaching Resources</h3>
        <div className="cce-card-grid">
            <div className="cce-card" style={{border: '1px solid #ddd'}}>
                <h4>Welcome Pack</h4>
                <p>PDF Guide for new clients</p>
                <button className="button">Download</button>
            </div>
            <div className="cce-card" style={{border: '1px solid #ddd'}}>
                <h4>Sales Training</h4>
                <p>Video series on high-ticket sales</p>
                <button className="button">Watch</button>
            </div>
        </div>
        <button className="button button-primary" style={{marginTop: '20px'}}>Add New Resource</button>
    </div>
);

const ProofView = () => (
    <div className="cce-card">
        <h3>Testimonials & Case Studies</h3>
        <div className="cce-card-grid">
            <div className="cce-card" style={{border: '1px solid #ddd'}}>
                <p>"The Coach Client Engine tripled my bookings in one month!"</p>
                <strong>- Sarah Jenkins</strong>
            </div>
        </div>
        <button className="button button-primary" style={{marginTop: '20px'}}>Request Testimonial</button>
    </div>
);

const SettingsView = () => (
    <div className="cce-card">
        <h3>General Settings</h3>
        <div className="form-group" style={{marginBottom: '20px'}}>
            <label style={{display: 'block', marginBottom: '5px'}}>License Key</label>
            <input type="text" className="regular-text" defaultValue={window.cceData?.licenseKey || ''} placeholder="PRO-XXXX-XXXX" />
            <p className="description">Enter your license key to unlock Pro features.</p>
        </div>
        <div className="form-group" style={{marginBottom: '20px'}}>
            <label style={{display: 'block', marginBottom: '5px'}}>Stripe API Key</label>
            <input type="password" title="Stripe API Key" className="regular-text" value="************" readOnly />
        </div>
        <button className="button button-primary">Save Settings</button>
    </div>
);

export default App;
