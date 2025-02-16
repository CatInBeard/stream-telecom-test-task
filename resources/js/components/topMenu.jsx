import React, {useState} from 'react';

const TopMenu = ({page,setPage, getToken, setToken}) => {

    const handleGoLink = () => {
        setPage('home');
    }

    const handleGoProfile = () => {
        setPage('profile');
    }

    const handleLogout = async () => {
        const token = getToken();

        try {
            const response = await fetch('/api/auth', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
            });

            if (response.ok) {
                console.log('Logout successful');
                setToken(null)
                setPage('welcome');
            } else {
                const errorData = await response.json();
                console.error('Logout failed:', errorData);
            }
        } catch (error) {
            console.error('Error during logout:', error);
        }
    }

    return <div>
                <div>
                    <div className={"btn btn-danger float-end ml-2 mr-2"} onClick={handleLogout}>Logout</div>
                    {page !== 'home' && <div className={"btn btn-primary float-end ml-2 mr-2"} onClick={handleGoLink}>Link management</div>}
                    {page !== 'profile' && <div className={"btn btn-secondary float-end ml-2 mr-2"} onClick={handleGoProfile}>Profile</div>}
                </div>
            </div>;
}

export default TopMenu
