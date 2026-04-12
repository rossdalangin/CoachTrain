import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Automation = () => {
    const [rules, setRules] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/automation/rules' }).then(res => {
            if (res.success) setRules(res.data);
        });
    }, []);

    return (
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
                    {rules.map(rule => (
                        <tr key={rule.id}>
                            <td>{rule.trigger}</td>
                            <td>{rule.action}</td>
                            <td>Active</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <button className="button button-primary" style={{marginTop: '20px'}}>Create New Workflow</button>
        </div>
    );
};

export default Automation;
