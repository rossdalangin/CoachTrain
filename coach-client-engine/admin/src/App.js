import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';
import Dashboard from './components/Dashboard';
import Leads from './components/Leads';
import CRM from './components/CRM';
import Funnels from './components/Funnels';
import Settings from './components/Settings';
import Clients from './components/Clients';
import Analytics from './components/Analytics';
import Wizard from './components/Wizard';
import Bookings from './components/Bookings';
import Automation from './components/Automation';
import Portal from './components/Portal';
import Proof from './components/Proof';

const App = () => {
    const [currentTab, setCurrentTab] = useState('Dashboard');
    const [isPro, setIsPro] = useState(window.cceData?.isPro || false);
    const [onboardingStep, setOnboardingStep] = useState(window.cceData?.onboarding_step || 1);

    const tabs = [
        'Dashboard', 'Leads', 'Bookings', 'Clients', 'Funnels',
        'Automation', 'CRM', 'Client Portal', 'Proof', 'Analytics', 'Settings'
    ];

    useEffect(() => {
        if (window.cceData?.nonce) {
            apiFetch.use(apiFetch.createNonceMiddleware(window.cceData.nonce));
            apiFetch.use(apiFetch.createRootURLMiddleware(window.cceData.root));
        }
    }, []);

    const completeOnboarding = () => {
        apiFetch({
            path: '/cce/v1/settings',
            method: 'POST',
            data: { onboarding_step: 0 }
        }).then(() => {
            setOnboardingStep(0);
        });
    };

    return (
        <div className="cce-admin-wrapper">
            <aside className="cce-sidebar">
                <div className="cce-logo cce-brand-logo" style={{padding: '20px', fontSize: '20px', borderBottom: '1px solid #333', textAlign: 'center'}}>
                    Coach Engine
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

                <div className="cce-tab-content">
                    {onboardingStep > 0 && currentTab === 'Dashboard' ? (
                        <Wizard onComplete={completeOnboarding} />
                    ) : (
                        <>
                            {currentTab === 'Dashboard' && <Dashboard setCurrentTab={setCurrentTab} />}
                            {currentTab === 'Leads' && <Leads />}
                            {currentTab === 'CRM' && <CRM />}
                            {currentTab === 'Funnels' && <Funnels />}
                            {currentTab === 'Settings' && <Settings setIsPro={setIsPro} />}
                            {currentTab === 'Clients' && <Clients />}
                            {currentTab === 'Analytics' && <Analytics />}
                            {currentTab === 'Bookings' && <Bookings />}
                            {currentTab === 'Automation' && <Automation />}
                            {currentTab === 'Client Portal' && <Portal />}
                            {currentTab === 'Proof' && <Proof />}
                        </>
                    )}
                </div>
            </main>
        </div>
    );
};

export default App;
