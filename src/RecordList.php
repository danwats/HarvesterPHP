<?php

declare(strict_types=1);

namespace DNS\Harvester;

class RecordList
{
    public array $records = [];

    public function add(Record $record)
    {
        $this->records[] = $record;
    }

    public function countTypes(): int
    {
        $v = 0;
        foreach ($this->records as $list) {
            $v += count($list->getValues());
        }
        return $v;
    }

    public function loadDefaults(): array
    {
        $this->add(new Record('@', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX, RecordType::NS, RecordType::SOA]));
        $this->add(new Record('www', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX]));
        $this->add(new Record('ns1', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ns2', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ns3', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ns4', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ns5', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ns6', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('autodiscover', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('enterpriseenrollment', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('enterpriseregistration', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('lyncdiscover', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('msoid', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('sip', [RecordType::CNAME]));
        $this->add(new Record('_sip._tls', [RecordType::SRV]));
        $this->add(new Record('_autodiscover._tcp', [RecordType::SRV]));
        $this->add(new Record('_sipfederationtls._tcp', [RecordType::SRV]));
        $this->add(new Record('ipfederationtls', [RecordType::SRV]));
        $this->add(new Record('_dmarc', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('dkim', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('_acme-challenge', [RecordType::TXT, RecordType::CNAME]));
        $this->add(new Record('mail', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX]));
        $this->add(new Record('ftp', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX]));
        $this->add(new Record('gmail', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('docs', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('calendar', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('webmail', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('smtp', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_domainconnect', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('staging', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('localhost', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('mailserver', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('mcp', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('imap', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('exchange', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('pop', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('blog', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('www1', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('remote', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('dev', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('cpanel', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('webdisk', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('autoconfig', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('whm', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('pop3', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('news', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('selector2._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('selector1._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('newsletter', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::MX]));
        $this->add(new Record('mobilemail', [RecordType::TXT, RecordType::CNAME]));
        $this->add(new Record('cpcalendars', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::MX]));
        $this->add(new Record('cpcontacts', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::MX]));
        $this->add(new Record('default._domainkey', [RecordType::TXT, RecordType::CNAME]));
        $this->add(new Record('s1._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('s2._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('s1._domainkey.email', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('s2._domainkey.email', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_autodiscover._tcp', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_acme-challenge.www', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('cp', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ebay', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendesk', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendesk1', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendesk2', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendesk3', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendesk4', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_amazonses', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('zendeskverification', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('metrics', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('mail._domainkey', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('google._domainkey', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('_domainkey.email', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('_domainkey', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('cm._domainkey', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('_carddav._tcp', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('pureresponse._domainkey.email', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('_caldav._tcp', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('_caldavs._tcp', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('xmpp-server._tcp', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('k3._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('k2._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('crm', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('shop', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_sipfederationtls', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('mandrill._domainkey', [RecordType::CNAME, RecordType::TXT, RecordType::SRV]));
        $this->add(new Record('wildcard', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('stats', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('webstats', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('personalise', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('personalize', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('old', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('sftp', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('ssh', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('email', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('cflinks', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('cfmail', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('fax', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('files', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('mobilemail', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('offers', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('www.admin', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('m1._domainkey', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('m1._domainkey.www', [RecordType::CNAME, RecordType::TXT]));
        $this->add(new Record('sales', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('track.sl', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('sl._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_dmarc.sl', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('sl', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('kl._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('kl2._domainkey', [RecordType::A, RecordType::AAAA, RecordType::CNAME]));
        $this->add(new Record('_dep_ws_mutex', [RecordType::TXT]));
        $this->add(new Record('_dep_ws_mutex.www', [RecordType::TXT]));
        $this->add(new Record('g01._domainkey', [RecordType::CNAME]));
        $this->add(new Record('g012._domainkey', [RecordType::CNAME]));
        $this->add(new Record('g013._domainkey', [RecordType::CNAME]));
        $this->add(new Record('mailerg01', [RecordType::CNAME]));
        $this->add(new Record('esp', [RecordType::CNAME]));
        $this->add(new Record('emailom', [RecordType::CNAME]));
        $this->add(new Record('clickon', [RecordType::CNAME]));
        $this->add(new Record('prod', [RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT]));
        return $this->records;
    }
}
