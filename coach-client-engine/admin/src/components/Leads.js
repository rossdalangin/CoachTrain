import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Leads = () => {
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

export default Leads;
