import React, {useEffect, useState} from 'react';

const Profile = ({setPage, getToken}) => {

    const [formData, setFormData] = useState({
        userId: '',
        email: '',
        name: '',
        password: '',
        passwordConfirm: '',
    });

    let token = getToken();

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const response = await fetch('/api/users/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    setFormData({
                        email: data.email || '',
                        name: data.name || '',
                        role: data.role || '',
                        userId: data.id || '',
                        password: '',
                        passwordConfirm: '',
                        tgName: data.tgName || '',
                        phone: data.phone || '',
                    });
                } else {
                    const error = await response.json();
                    setError('Failed to fetch user data');
                    console.error('Error:', error);
                }
            } catch (error) {
                console.error('Error:', error);
                setError('An error occurred while fetching user data');
            }
        };

        fetchUserData();
    }, [token]);

    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccess('')

        if (formData.password && formData.password !== formData.passwordConfirm) {
            setError('Password does not match');
            setIsSubmitting(false);
            return;
        }

        const data = new FormData();
        data.append('name', formData.name);
        data.append('email', formData.email);
        if(formData.password){
            data.append('password', formData.password);
        }

        try {
            const response = await fetch('/api/users/' + formData.userId , {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: data,
            });

            if (response.ok) {
                const result = await response.text();
                console.log('Success:', result);
                setSuccess('Profile updated')
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Can\'t update profile, ' + Object.values(error.errors)
                    .flatMap(messages => messages)
                    .map(message => `"${message}"`));
                console.error('Error:', error);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div>
            <h1 className={"text-center"}>Your profile edit page</h1>
            <br/>
            <p>Here you can see your profile</p>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit} >
                <div className="form-group">
                    <label htmlFor="email">Email address</label>
                    <input type="email" className="form-control" id="email" aria-describedby="emailHelp" onChange={handleChange}
                           placeholder="Enter email" name="email" disabled={isSubmitting} value={formData.email}/>
                    <small id="emailHelp" className="form-text text-muted">We'll never share your email with anyone
                        else.</small>
                </div>
                <div className="form-group">
                    <label htmlFor="name">Name</label>
                    <input type="text" className="form-control" id="name" onChange={handleChange}
                           placeholder="Enter name" name="name" disabled={isSubmitting} value={formData.name}/>
                </div>
                <div className="form-group">
                    <label htmlFor="password">Password</label>
                    <input type="password" className="form-control" id="password" placeholder="Password" onChange={handleChange}
                           name="password" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="passwordConfirm">Password confirm</label>
                    <input type="password" className="form-control" id="passwordConfirm" placeholder="Password" onChange={handleChange}
                           name="passwordConfirm" disabled={isSubmitting}/>
                </div>

                <button type="submit" className="btn btn-primary mt-2">Submit</button>
            </form>
        </div>
    );
};

export default Profile;
