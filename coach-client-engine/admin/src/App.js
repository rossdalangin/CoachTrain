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

                {currentTab === 'Dashboard' && <DashboardView setCurrentTab={setCurrentTab} />}
                {currentTab === 'Leads' && <LeadsView />}
                {currentTab === 'CRM' && <CRMView />}
                {currentTab === 'Automation' && <AutomationView />}
                {currentTab === 'Client Portal' && <PortalView />}
                {currentTab === 'Proof' && <ProofView />}
                {currentTab === 'Settings' && <SettingsView setIsPro={setIsPro} />}
                {currentTab === 'Funnels' && <FunnelsView />}
                {currentTab === 'Clients' && <ClientsView />}

                {!['Dashboard', 'Leads', 'CRM', 'Automation', 'Client Portal', 'Proof', 'Settings', 'Funnels', 'Clients'].includes(currentTab) &&
                    <div className="cce-card">
                        <p>The <strong>{currentTab}</strong> module is currently in development and will be available in the next update.</p>
                        <button className="button">Join the Beta</button>
                    </div>
                }
            </main>
        </div>
    );
};

const DashboardView = ({ setCurrentTab }) => {
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

const FunnelsView = () => {
    const [funnelSteps, setFunnelSteps] = useState([]);
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        // Mock loading steps for funnel ID 1
        apiFetch({ path: '/cce/v1/funnels/1/steps' }).then(res => {
            if (res.success) setFunnelSteps(res.data);
        });
    }, []);

    const addStep = () => {
        const newStep = { id: Date.now(), title: 'New Step', type: 'optin' };
        setFunnelSteps([...funnelSteps, newStep]);
    };

    const removeStep = (id) => {
        setFunnelSteps(funnelSteps.filter(step => step.id !== id));
    };

    const handleSave = () => {
        setIsSaving(true);
        apiFetch({
            path: '/cce/v1/funnels/1/steps',
            method: 'POST',
            data: { steps: funnelSteps }
        }).then(() => {
            alert('Funnel steps saved!');
            setIsSaving(false);
        });
    };

    return (
        <div>
            <div className="cce-card" style={{marginBottom: '20px'}}>
                <h3>Your Funnel Flow</h3>
                <div className="funnel-steps" style={{display: 'flex', flexDirection: 'column', gap: '10px'}}>
                    {funnelSteps.map((step, index) => (
                        <div key={step.id} className="funnel-step-card" style={{
                            background: '#fff',
                            border: '1px solid #ddd',
                            padding: '15px',
                            borderRadius: '4px',
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center'
                        }}>
                            <div>
                                <span style={{fontWeight: 'bold', marginRight: '10px'}}>#{index + 1}</span>
                                {step.title} <span style={{fontSize: '12px', color: '#666'}}>({step.type || step.step_type})</span>
                            </div>
                            <button className="button" onClick={() => removeStep(step.id)}>Remove</button>
                        </div>
                    ))}
                </div>
                <div style={{marginTop: '20px', display: 'flex', gap: '10px'}}>
                    <button className="button button-primary" onClick={addStep}>+ Add Step</button>
                    <button className="button" onClick={handleSave} disabled={isSaving}>{isSaving ? 'Saving...' : 'Save Flow'}</button>
                </div>
            </div>

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
};

const ClientsView = () => {
    const [offers, setOffers] = useState([
        { id: 1, title: '90-Day Transformation', price: 2997, type: 'one-time' },
        { id: 2, title: 'Monthly Mentorship', price: 497, type: 'subscription' }
    ]);

    return (
        <div className="cce-card">
            <h3>Your Coaching Offers</h3>
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {offers.map(offer => (
                        <tr key={offer.id}>
                            <td><strong>{offer.title}</strong></td>
                            <td>${offer.price}</td>
                            <td>{offer.type.toUpperCase()}</td>
                            <td><button className="button button-small">Edit</button></td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <button className="button button-primary" style={{marginTop: '20px'}}>Create New Offer</button>
        </div>
    );
};

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

    const fetchPipeline = () => {
        apiFetch({ path: '/cce/v1/crm/pipeline' }).then(response => {
            if (response.success) {
                setPipeline(response.data);
            }
        });
    };

    useEffect(() => {
        fetchPipeline();
    }, []);

    const handleContact = (id) => {
        const message = prompt('Enter message to send:');
        if (message) {
            apiFetch({
                path: `/cce/v1/crm/leads/${id}/contact`,
                method: 'POST',
                data: { message }
            }).then(() => {
                alert('Contact logged!');
                fetchPipeline();
            });
        }
    };

    const handleStageChange = (leadId, newStageId) => {
        apiFetch({
            path: `/cce/v1/crm/leads/${leadId}/stage`,
            method: 'POST',
            data: { stage_id: newStageId }
        }).then(() => {
            fetchPipeline();
        });
    };

    const handleStatusChange = (leadId, newStatus) => {
        apiFetch({
            path: `/cce/v1/leads/${leadId}/status`,
            method: 'POST',
            data: { status: newStatus }
        }).then(() => {
            fetchPipeline();
        });
    };

    return (
        <div className="cce-kanban" style={{display: 'flex', gap: '20px', overflowX: 'auto', paddingBottom: '20px'}}>
            {pipeline.map(item => (
                <div key={item.stage.id} className="kanban-column" style={{minWidth: '250px', background: '#e2e8f0', borderRadius: '8px', padding: '15px'}}>
                    <h3 style={{marginTop: 0, fontSize: '16px', color: '#4a5568'}}>{item.stage.name} ({item.leads.length})</h3>
                    <div className="kanban-cards">
                        {item.leads.map(lead => (
                            <div key={lead.id} className="cce-card" style={{marginBottom: '10px', fontSize: '14px', padding: '10px'}}>
                                <div style={{display: 'flex', justifyContent: 'space-between', alignItems: 'start'}}>
                                    <strong>{lead.first_name} {lead.last_name}</strong>
                                    <select
                                        style={{fontSize: '10px', height: '20px', border: 'none', background: '#edf2f7'}}
                                        value={lead.status}
                                        onChange={(e) => handleStatusChange(lead.id, e.target.value)}
                                    >
                                        <option value="cold">COLD</option>
                                        <option value="warm">WARM</option>
                                        <option value="hot">HOT</option>
                                    </select>
                                </div>
                                {lead.activities?.length > 0 && (
                                    <div style={{fontSize: '11px', marginTop: '5px', color: '#0073aa', fontStyle: 'italic'}}>
                                        Latest: {lead.activities[0].activity_type}
                                    </div>
                                )}
                                <div style={{marginTop: '10px', display: 'flex', gap: '5px'}}>
                                    <button className="button button-small" onClick={() => handleContact(lead.id)}>Contact</button>
                                    <select
                                        style={{fontSize: '11px', height: '26px'}}
                                        onChange={(e) => handleStageChange(lead.id, e.target.value)}
                                        value={item.stage.id}
                                    >
                                        {pipeline.map(p => <option key={p.stage.id} value={p.stage.id}>{p.stage.name}</option>)}
                                    </select>
                                </div>
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
    const [settings, setSettings] = useState({ license_key: '', stripe_api_key: '' });
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/settings' }).then(response => {
            if (response.success) {
                setSettings(response.data);
            }
        });
    }, []);

    const handleSave = () => {
        setIsSaving(true);
        apiFetch({
            path: '/cce/v1/settings',
            method: 'POST',
            data: settings
        }).then(response => {
            if (response.success) {
                setIsPro(settings.license_key.startsWith('PRO-'));
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
                    value={settings.license_key}
                    onChange={(e) => setSettings({...settings, license_key: e.target.value})}
                    placeholder="PRO-XXXX-XXXX"
                />
                <p className="description">Enter your license key to unlock Pro features.</p>
            </div>
            <div className="form-group" style={{marginBottom: '20px'}}>
                <label style={{display: 'block', marginBottom: '5px'}}>Stripe API Key</label>
                <input
                    type="password"
                    className="regular-text"
                    value={settings.stripe_api_key}
                    onChange={(e) => setSettings({...settings, stripe_api_key: e.target.value})}
                />
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
