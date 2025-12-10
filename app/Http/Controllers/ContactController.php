<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;
use App\Mail\PartnershipFormMail;
use App\Http\Requests\ContactFormRequest;
use App\Http\Requests\PartnershipFormRequest;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function submitContactForm(ContactFormRequest $request)
    {
        try {
            $formData = $request->validated();
            
            // Add timestamp and IP address for tracking
            $formData['submitted_at'] = now()->format('Y-m-d H:i:s');
            $formData['ip_address'] = $request->ip();
            $formData['user_agent'] = $request->userAgent();
            
            // Send email to hello@olilearn.com
            Mail::to('hello@olilearn.com')
                ->cc('support@olilearn.com') // Optional: CC to support team
                ->send(new ContactFormMail($formData));
            
            // Store in database if needed (optional)
            // ContactSubmission::create($formData);
            
            Log::info('Contact form submitted successfully', [
                'email' => $formData['email'],
                'subject' => $formData['subject'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you within 24 hours.',
                'data' => [
                    'first_name' => $formData['first_name'],
                    'email' => $formData['email'],
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again later or email us directly at hello@olilearn.com',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Handle partnership form submission
     */
    public function submitPartnershipForm(PartnershipFormRequest $request)
    {
        try {
            $formData = $request->validated();
            
            // Add additional metadata
            $formData['submitted_at'] = now()->format('Y-m-d H:i:s');
            $formData['ip_address'] = $request->ip();
            $formData['user_agent'] = $request->userAgent();
            
            // Determine priority based on budget
            $formData['priority'] = $this->determinePriority($formData['csr_budget']);
            
            // Send email to multiple recipients
            $recipients = [
                'hello@olilearn.com',
                'hello@olilearn.com', // Partnership-specific email
            ];
            
            Mail::to($recipients)
                ->send(new PartnershipFormMail($formData));
            
            // Optional: Also send confirmation to the company
            // Mail::to($formData['email'])->send(new PartnershipConfirmationMail($formData));
            
            Log::info('Partnership form submitted successfully', [
                'company' => $formData['company_name'],
                'contact' => $formData['contact_name'],
                'budget' => $formData['csr_budget'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your partnership interest! Our corporate relations team will contact you within 24 business hours.',
                'data' => [
                    'company_name' => $formData['company_name'],
                    'contact_name' => $formData['contact_name'],
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Partnership form submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error processing your partnership request. Please try again later or email us directly at partnerships@olilearn.com',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Determine priority level based on CSR budget
     */
    private function determinePriority($budget)
    {
        $priorityMap = [
            '1-5M' => 'Medium',
            '5-10M' => 'High',
            '10-25M' => 'High',
            '25-50M' => 'Urgent',
            '50M+' => 'Urgent',
        ];
        
        return $priorityMap[$budget] ?? 'Medium';
    }
    
    /**
     * Handle newsletter subscription
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ]);
        
        try {
            // Store in database or newsletter service
            // NewsletterSubscriber::create(['email' => $request->email]);
            
            // Send confirmation email
            // Mail::to($request->email)->send(new NewsletterConfirmationMail());
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to our newsletter!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed. Please try again.'
            ], 500);
        }
    }
}