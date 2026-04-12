import React from 'react';

const Wizard = ({ onComplete }) => {
    return (
        <div className="cce-wizard cce-card" style={{border: '2px solid #0073aa', background: '#fff'}}>
            <h2>🧙‍♂️ Welcome to the Client Engine</h2>
            <p>Let's set up your client-getting machine in 3 minutes.</p>
            <div className="wizard-steps">
                <div className="step">
                    <h3>1. The Offer</h3>
                    <p>What are you selling? (e.g. $5k Coaching)</p>
                </div>
                <div className="step">
                    <h3>2. The Funnel</h3>
                    <p>Pick a template to start getting leads.</p>
                </div>
            </div>
            <button className="button button-primary button-hero" onClick={onComplete}>Launch My Engine</button>
        </div>
    );
};

export default Wizard;
