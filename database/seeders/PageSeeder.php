<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update or create About Us page
        Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => '<p>Welcome to our Waitinglist application. We are dedicated to providing the best restaurant waiting list management solution.</p>
                              <p>Our platform helps restaurants efficiently manage their customer waiting lists, reducing wait times and improving customer satisfaction.</p>',
                'is_active' => true,
            ]
        );

        // Update or create Terms of Service page
        Page::updateOrCreate(
            ['slug' => 'terms-of-service'],
            [
                'title' => 'Terms of Service',
                'content' => '<h2>Terms of Service</h2>
                              <p>These Terms of Service ("Terms") govern your access to and use of the Waitinglist application and services ("Services") provided by Waitinglist ("we," "us," or "our"). By accessing or using our Services, you agree to be bound by these Terms and our Privacy Policy.</p>
                              
                              <h3>1. Acceptance of Terms</h3>
                              <p>By accessing or using our Services, you confirm that you are at least 18 years old and have the legal authority to enter into these Terms. If you are using our Services on behalf of an organization, you agree to these Terms on behalf of that organization.</p>
                              
                              <h3>2. Description of Services</h3>
                              <p>Waitinglist provides restaurant waiting list management solutions, including but not limited to:</p>
                              <ul>
                                <li>Customer waitlist management</li>
                                <li>Real-time queue updates</li>
                                <li>Customer notifications</li>
                                <li>Analytics and reporting</li>
                                <li>Staff management tools</li>
                              </ul>
                              
                              <h3>3. Account Registration</h3>
                              <p>To access certain features of our Services, you may be required to create an account. You agree to:</p>
                              <ul>
                                <li>Provide accurate, current, and complete information</li>
                                <li>Maintain and update your information as needed</li>
                                <li>Maintain the security of your password</li>
                                <li>Notify us immediately of any unauthorized use of your account</li>
                              </ul>
                              
                              <h3>4. Acceptable Use</h3>
                              <p>You agree not to:</p>
                              <ul>
                                <li>Use the Services for any illegal purpose</li>
                                <li>Violate any applicable laws or regulations</li>
                                <li>Interfere with or disrupt the Services</li>
                                <li>Attempt to gain unauthorized access to any part of the Services</li>
                                <li>Use the Services to transmit any harmful or malicious code</li>
                              </ul>
                              
                              <h3>5. Intellectual Property</h3>
                              <p>All content, features, and functionality of our Services are owned by Waitinglist and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
                              
                              <h3>6. Termination</h3>
                              <p>We may terminate or suspend your access to our Services immediately, without prior notice or liability, for any reason, including if you breach these Terms.</p>
                              
                              <h3>7. Disclaimer of Warranties</h3>
                              <p>Our Services are provided "as is" and "as available" without warranties of any kind, either express or implied.</p>
                              
                              <h3>8. Limitation of Liability</h3>
                              <p>In no event shall Waitinglist be liable for any indirect, incidental, special, consequential, or punitive damages.</p>
                              
                              <h3>9. Changes to Terms</h3>
                              <p>We reserve the right to modify these Terms at any time. We will notify you of any changes by posting the new Terms on this page.</p>
                              
                              <h3>10. Contact Information</h3>
                              <p>If you have any questions about these Terms, please contact us at support@waitinglist.app.</p>',
                'is_active' => true,
            ]
        );

        // Update or create Privacy Policy page
        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<h2>Privacy Policy</h2>
                              <p>This Privacy Policy describes how Waitinglist ("we," "us," or "our") collects, uses, and shares your personal information when you use our Waitinglist application and services ("Services").</p>
                              
                              <h3>1. Information We Collect</h3>
                              <p>We collect information you provide directly to us, including:</p>
                              <ul>
                                <li>Name and contact information</li>
                                <li>Business information (if you are a restaurant owner)</li>
                                <li>Account credentials</li>
                                <li>Payment information</li>
                                <li>Communications with our support team</li>
                              </ul>
                              
                              <p>We also automatically collect information about your use of our Services, including:</p>
                              <ul>
                                <li>Device information (IP address, browser type, operating system)</li>
                                <li>Usage data (pages visited, features used, time spent)</li>
                                <li>Log information</li>
                              </ul>
                              
                              <h3>2. How We Use Your Information</h3>
                              <p>We use your information to:</p>
                              <ul>
                                <li>Provide, maintain, and improve our Services</li>
                                <li>Process transactions and send transactional notifications</li>
                                <li>Communicate with you about our Services</li>
                                <li>Provide customer support</li>
                                <li>Monitor and analyze usage patterns</li>
                                <li>Prevent fraud and enhance security</li>
                              </ul>
                              
                              <h3>3. Sharing Your Information</h3>
                              <p>We may share your information with:</p>
                              <ul>
                                <li>Service providers who assist us in operating our Services</li>
                                <li>Payment processors to process transactions</li>
                                <li>Legal authorities when required by law</li>
                                <li>Professional advisors such as lawyers and accountants</li>
                              </ul>
                              
                              <h3>4. Data Security</h3>
                              <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                              
                              <h3>5. Data Retention</h3>
                              <p>We retain your personal information for as long as necessary to provide our Services and comply with legal obligations.</p>
                              
                              <h3>6. Your Rights</h3>
                              <p>Depending on your location, you may have rights regarding your personal information, including:</p>
                              <ul>
                                <li>The right to access your personal information</li>
                                <li>The right to rectify inaccurate personal information</li>
                                <li>The right to erase your personal information</li>
                                <li>The right to restrict processing of your personal information</li>
                                <li>The right to data portability</li>
                              </ul>
                              
                              <h3>7. Cookies and Tracking Technologies</h3>
                              <p>We use cookies and similar tracking technologies to enhance your experience and analyze usage patterns.</p>
                              
                              <h3>8. Children\'s Privacy</h3>
                              <p>Our Services are not intended for individuals under the age of 18.</p>
                              
                              <h3>9. International Data Transfers</h3>
                              <p>Your information may be transferred to and processed in countries other than your own.</p>
                              
                              <h3>10. Changes to This Privacy Policy</h3>
                              <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                              
                              <h3>11. Contact Information</h3>
                              <p>If you have any questions about this Privacy Policy, please contact us at privacy@waitinglist.app.</p>',
                'is_active' => true,
            ]
        );
    }
}