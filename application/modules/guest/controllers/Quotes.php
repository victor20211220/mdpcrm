<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotes extends Guest_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotes/Mdl_quotes');
    }

    public function index()
    {
        // Display open quotes by default
        redirect('guest/quotes/status/open');
    }

    public function status($status = 'open', $page = 0)
    {
        redirect_to_set();

        // Determine which group of quotes to load
        switch ($status) {
            case 'approved' :
                $this->Mdl_quotes->is_approved()->where_in('ip_quotes.client_id', $this->user_clients);
                break;
            case 'rejected' :
                $this->Mdl_quotes->is_rejected()->where_in('ip_quotes.client_id', $this->user_clients);
                $this->layout->set('show_invoice_column', true);
                break;
            default :
                $this->Mdl_quotes->is_open()->where_in('ip_quotes.client_id', $this->user_clients);
                break;
        }

        $this->Mdl_quotes->paginate(site_url('guest/quotes/status/' . $status), $page);
        $quotes = $this->Mdl_quotes->result();

        $this->layout->set('quotes', $quotes);
        $this->layout->set('status', $status);
        $this->layout->buffer('content', 'guest/quotes_index');
        $this->layout->render('layout_guest');
    }

    public function view($quote_id)
    {
        redirect_to_set();

        $this->load->model('quotes/Mdl_quote_items');
        $this->load->model('quotes/Mdl_quote_tax_rates');

        $quote = $this->Mdl_quotes->guest_visible()->where('ip_quotes.quote_id',
            $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (!$quote) {
            show_404();
        }

        $this->Mdl_quotes->mark_viewed($quote->quote_id);

        $this->layout->set([
            'quote'           => $quote,
            'items'           => $this->Mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
            'quote_tax_rates' => $this->Mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
            'quote_id'        => $quote_id
        ]);

        $this->layout->buffer('content', 'guest/quotes_view');
        $this->layout->render('layout_guest');
    }

    public function generate_pdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');
        $this->Mdl_quotes->mark_viewed($quote_id);
        $quote = $this->Mdl_quotes->guest_visible()->where('ip_quotes.quote_id',
            $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (!$quote) {
            show_404();
        } else {
            generate_quote_pdf($quote_id, $stream, $quote_template);
        }
    }

    public function approve($quote_id)
    {
        $this->load->model('quotes/Mdl_quotes');
        $this->load->helper('mailer');

        $this->Mdl_quotes->approve_quote_by_id($quote_id);
        email_quote_status($quote_id, "approved");

        redirect_to('guest/quotes');
    }

    public function reject($quote_id)
    {
        $this->load->model('quotes/Mdl_quotes');
        $this->load->helper('mailer');

        $this->Mdl_quotes->reject_quote_by_id($quote_id);
        email_quote_status($quote_id, "rejected");

        redirect_to('guest/quotes');
    }
}
