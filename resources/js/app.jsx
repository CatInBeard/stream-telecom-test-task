import './bootstrap';

import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom/client';
import 'bootstrap/dist/css/bootstrap.min.css';
import Welcome from './components/welcome.jsx';
import Login from "./components/login.jsx";
import Home from "./components/home.jsx"
import TopMenu from "./components/topMenu.jsx";
import Profile from "./components/profile.jsx";

const App = () => {
    const [page, setPage] = useState('welcome');
    const [token, setToken] = useState(() => {
        return localStorage.getItem('token') || null;
    });

    const getToken = () => {
        return token;
    };

    const saveToken = (newToken) => {
        setToken(newToken);
        localStorage.setItem('token', newToken);
    };

    const checkToken = async () => {
        if (token) {
            try {
                const response = await fetch('/api/users/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.status === 401) {
                    localStorage.removeItem('token');
                    setToken(null);
                    setPage('welcome');
                }
            } catch (error) {
                console.error('Failed to validate token:', error);
            }
        }
    };

    useEffect(() => {
        checkToken();
    }, [token]);

    return (
        <div className={"container-sm mt-5"} style={{
            minHeight: '80vh',
            borderLeft: '1px solid #CCCCCC',
            borderRight: '1px solid #CCCCCC'
        }}>
            {token !== null && <TopMenu page={page} setPage={setPage} getToken={getToken} setToken={setToken}/> }
            {page === 'welcome' && <Welcome setPage={setPage} getToken={getToken} /> }
            {page === 'login' && <Login setPage={setPage} setToken={saveToken} getToken={getToken} />}
            {page === 'home' && <Home setPage={setPage} getToken={getToken} />}
            {page === 'profile' && <Profile setPage={setPage} getToken={getToken} />}
        </div>
    );
};

ReactDOM.createRoot(document.getElementById('react-app')).render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
