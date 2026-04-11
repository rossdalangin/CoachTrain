import { render } from '@wordpress/element';
import App from './App';

const container = document.getElementById('cce-admin-app');
if (container) {
    render(<App />, container);
}
