import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const App = () => {
    const [currentTab, setCurrentTab] = useState('Dashboard');
    const [isPro, setIsPro] = useState(window.cceData?.isPro || false);

    const tabs = [
        'Dashboard', 'Leads', 'Bookings', 'Clients', 'Funnels',
        'Automation', 'CRM', 'Client Portal', 'Proof', 'Analytics', 'Settings'
    ];

    // Configure apiFetch
    useEffect(() => {
        if (window.cceData?.nonce) {
            apiFetch.use(apiFetch.createNonceMiddleware(window.cceData.nonce));
            apiFetch.use(apiFetch.createRootURLMiddleware(window.cceData.root));
        }
    }, []);

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
                        {isPro ? <span className="badge-pro" style={{background: '#ffc107', color: '#000', padding: '4px 8px', borderRadius: '4px', fontSize: '12px', fontWeight: 'bold'}}>PRO</span> : <button className="button button-primary">Upgrade to Pro</button>}
                    </div>
                </header>

                {currentTab === 'Dashboard' && <DashboardView />}
                {currentTab === 'Leads' && <LeadsView />}
                {currentTab === 'CRM' && <CRMView />}
                {currentTab === 'Automation' && <AutomationView />}
                {currentTab === 'Client Portal' && <PortalView />}
                {currentTab === 'Proof' && <ProofView />}
                {currentTab === 'Settings' && <SettingsView setIsPro={setIsPro} />}
                {currentTab === 'Funnels' && <FunnelsView />}

                {!['Dashboard', 'Leads', 'CRM', 'Automation', 'Client Portal', 'Proof', 'Settings', 'Funnels'].includes(currentTab) &&
                    <div className="cce-card">
                        <p>The <strong>{currentTab}</strong> module is currently in development and will be available in the next update.</p>
                        <button className="button">Join the Beta</button>
                    </div>
                }
            </main>
        </div>
    );
};

const DashboardView = () => {
    const [summary, setSummary] = useState({ leads: 0, bookings: 0, revenue: 0 });

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
                        <p>Define what you sell (e.g., "90-Day High-Ticket Coaching").</p>
                    </div>
                    <div style={{flex: 1}}>
                        <strong>2. Build Your Funnel</strong>
                        <p>Use our pre-built templates to start getting leads.</p>
                    </div>
                    <div style={{flex: 1}}>
                        <strong>3. Connect Payments</strong>
                        <p>Link Stripe or PayPal to start receiving funds.</p>
                    </div>
                </div>
            </div>

            <div className="cce-card-grid">
                <div className="cce-card">
                    <h3>Total Leads</h3>
                    <div className="value">{summary.leads}</div>
                </div>
                <div className="cce-card">
                    <h3>Total Bookings</h3>
                    <div className="value">{summary.bookings}</div>
                </div>
                <div className="cce-card">
                    <h3>Revenue</h3>
                    <div className="value">${summary.revenue.toLocaleString()}</div>
                </div>
                <div className="cce-card">
                    <h3>Conversion Rate</h3>
                    <div className="value">{summary.leads > 0 ? ((summary.bookings / summary.leads) * 100).toFixed(1) : 0}%</div>
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
};

const FunnelsView = () => (
    <div>
        <div className="cce-card">
            <h3>Pre-built Templates</h3>
            <div className="cce-card-grid">
                <div className="cce-card" style={{border: '1px solid #ddd'}}>
                    <h4>Lead Magnet Funnel</h4>
                    <p>Best for growing your list.</p>
                    <button className="button button-primary">Use Template</button>
                </div>
                <div className="cce-card" style={{border: '1px solid #ddd'}}>
                    <h4>Consultation Funnel</h4>
                    <p>Best for high-ticket bookings.</p>
                    <button className="button button-primary">Use Template</button>
                </div>
            </div>
        </div>
    </div>
);

const LeadsView = () => {
    const [leads, setLeads] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/leads' }).then(response => {
            if (response.success) {
                setLeads(response.data);
            }
        });
    }, []);

    return (
        <div className="cce-card">
            {leads.length === 0 ? <p>No leads found. Start by creating a funnel!</p> : (
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
                                <td><span className={`status-tag status-${lead.status}`}>{lead.status?.toUpperCase()}</span></td>
                                <td>{lead.created_at}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
        </div>
    );
};

const CRMView = () => {
    const [pipeline, setPipeline] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/crm/pipeline' }).then(response => {
            if (response.success) {
                setPipeline(response.data);
            }
        });
    }, []);

    return (
        <div className="cce-kanban" style={{display: 'flex', gap: '20px', overflowX: 'auto', paddingBottom: '20px'}}>
            {pipeline.map(item => (
                <div key={item.stage.id} className="kanban-column" style={{minWidth: '250px', background: '#e2e8f0', borderRadius: '8px', padding: '15px'}}>
                    <h3 style={{marginTop: 0, fontSize: '16px', color: '#4a5568'}}>{item.stage.name} ({item.leads.length})</h3>
                    <div className="kanban-cards">
                        {item.leads.map(lead => (
                            <div key={lead.id} className="cce-card" style={{marginBottom: '10px', fontSize: '14px', padding: '10px'}}>
                                <strong>{lead.first_name} {lead.last_name}</strong>
                                <div style={{fontSize: '12px', color: '#718096'}}>{lead.status?.toUpperCase()}</div>
                            </div>
                        ))}
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

const SettingsView = ({ setIsPro }) => {
    const [licenseKey, setLicenseKey] = useState(window.cceData?.licenseKey || '');
    const [isSaving, setIsSaving] = useState(false);

    const handleSave = () => {
        setIsSaving(true);
        apiFetch({
            path: '/cce/v1/settings/license',
            method: 'POST',
            data: { license_key: licenseKey }
        }).then(response => {
            if (response.success) {
                setIsPro(licenseKey.startsWith('PRO-'));
                alert('Settings saved successfully!');
            }
            setIsSaving(false);
        }).catch(err => {
            console.error(err);
            setIsSaving(false);
        });
    };

    return (
        <div className="cce-card">
            <h3>General Settings</h3>
            <div className="form-group" style={{marginBottom: '20px'}}>
                <label style={{display: 'block', marginBottom: '5px'}}>License Key</label>
                <input
                    type="text"
                    className="regular-text"
                    value={licenseKey}
                    onChange={(e) => setLicenseKey(e.target.value)}
                    placeholder="PRO-XXXX-XXXX"
                />
                <p className="description">Enter your license key to unlock Pro features.</p>
            </div>
            <div className="form-group" style={{marginBottom: '20px'}}>
                <label style={{display: 'block', marginBottom: '5px'}}>Stripe API Key</label>
                <input type="password" title="Stripe API Key" className="regular-text" value="************" readOnly />
            </div>
            <button
                className="button button-primary"
                onClick={handleSave}
                disabled={isSaving}
            >
                {isSaving ? 'Saving...' : 'Save Settings'}
            </button>
        </div>
    );
};

export default App;
