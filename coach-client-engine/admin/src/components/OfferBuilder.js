import React from 'react';

const OfferBuilder = () => {
    return (
        <div className="cce-card" style={{borderLeft: '4px solid #ff4136'}}>
            <h3>💎 Alex Hormozi "Grand Slam" Offer Builder</h3>
            <p>Use the Value Equation to create an irresistible offer.</p>
            <div className="value-equation-grid" style={{display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px'}}>
                <div className="field">
                    <label>Dream Outcome</label>
                    <input type="text" style={{width: '100%'}} placeholder="The big result..." />
                </div>
                <div className="field">
                    <label>Perceived Likelihood</label>
                    <input type="text" style={{width: '100%'}} placeholder="Guarantee/Proof..." />
                </div>
                <div className="field">
                    <label>Time Delay</label>
                    <input type="text" style={{width: '100%'}} placeholder="How fast?" />
                </div>
                <div className="field">
                    <label>Effort & Sacrifice</label>
                    <input type="text" style={{width: '100%'}} placeholder="What do they give up?" />
                </div>
            </div>
            <button className="button button-primary" style={{marginTop: '20px'}}>Calculate Offer Value</button>
        </div>
    );
};

export default OfferBuilder;
