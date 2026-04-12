import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const CRM = () => {
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
                    </div>
                </div>
            ))}
        </div>
    );
};

export default CRM;
