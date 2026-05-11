import React from 'react';
import MasterLayout from '../layouts/MasterLayout';

const PrivacyPolicy = () => {
    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <div className="card border-0 shadow-sm rounded-3">
                            <div className="card-body p-4 p-md-5">
                                <h2 className="fw-bold mb-4" style={{ color: '#333' }}>Privacy Policy</h2>
                                <p className="text-muted small mb-4">Last Updated: October 2023</p>

                                <div style={{ lineHeight: '1.8', color: '#555' }}>
                                    <h5 className="fw-bold text-dark mt-4">1. Information We Collect</h5>
                                    <p>We collect information you provide directly to us, such as when you create or modify your account, request on-demand services, contact customer support, or otherwise communicate with us. This information may include: name, email, phone number, postal address, profile picture, payment method, items requested, delivery notes, and other information you choose to provide.</p>

                                    <h5 className="fw-bold text-dark mt-4">2. How We Use Your Information</h5>
                                    <p>We may use the information we collect about you to: </p>
                                    <ul>
                                        <li>Provide, maintain, and improve our services.</li>
                                        <li>Process transactions and send related information, including confirmations and receipts.</li>
                                        <li>Send you technical notices, updates, security alerts, and support and administrative messages.</li>
                                        <li>Respond to your comments, questions, and requests and provide customer service.</li>
                                    </ul>

                                    <h5 className="fw-bold text-dark mt-4">3. Sharing of Information</h5>
                                    <p>We do not share personal information with companies, organizations, or individuals outside of JHR Bazar except in the following cases: With your consent, for external processing, or for legal reasons.</p>

                                    <h5 className="fw-bold text-dark mt-4">4. Security</h5>
                                    <p>We take reasonable measures to help protect information about you from loss, theft, misuse, and unauthorized access, disclosure, alteration, and destruction.</p>

                                    <h5 className="fw-bold text-dark mt-4">5. Your Choices</h5>
                                    <p>You may update, correct, or delete information about yourself at any time by logging into your online account or by contacting us.</p>

                                    <div className="mt-5 p-3 bg-light rounded text-center">
                                        <p className="mb-0 small fw-bold">If you have any questions about this Privacy Policy, please contact us at privacy@jhrbazar.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};

export default PrivacyPolicy;
