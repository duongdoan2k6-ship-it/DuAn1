<?php

class BaocaoModel extends BaseModel
{
    // =============================
    // CRUD CƠ BẢN
    // =============================

    public function getAllBaocao()
    {
        $sql = "SELECT * FROM baocaotaichinh ORDER BY BaoCaoID DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBaocaoById($id)
    {
        $sql = "SELECT * FROM baocaotaichinh WHERE BaoCaoID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertBaocao($data)
    {
        $sql = "INSERT INTO baocaotaichinh (MaLichKhoiHanh, DoanhThu, ChiPhi, LoiNhuan, NgayTaoBaoCao)
                VALUES (:MaLichKhoiHanh, :DoanhThu, :ChiPhi, :LoiNhuan, :NgayTaoBaoCao)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':MaLichKhoiHanh', $data['MaLichKhoiHanh']);
        $stmt->bindParam(':DoanhThu', $data['DoanhThu']);
        $stmt->bindParam(':ChiPhi', $data['ChiPhi']);
        $stmt->bindParam(':LoiNhuan', $data['LoiNhuan']);
        $stmt->bindParam(':NgayTaoBaoCao', $data['NgayTaoBaoCao']);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateBaocao($id, $data)
    {
        $sql = "UPDATE baocaotaichinh
                SET MaLichKhoiHanh = :MaLichKhoiHanh,
                    DoanhThu       = :DoanhThu,
                    ChiPhi         = :ChiPhi,
                    LoiNhuan       = :LoiNhuan,
                    NgayTaoBaoCao  = :NgayTaoBaoCao
                WHERE BaoCaoID = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':MaLichKhoiHanh', $data['MaLichKhoiHanh']);
        $stmt->bindParam(':DoanhThu', $data['DoanhThu']);
        $stmt->bindParam(':ChiPhi', $data['ChiPhi']);
        $stmt->bindParam(':LoiNhuan', $data['LoiNhuan']);
        $stmt->bindParam(':NgayTaoBaoCao', $data['NgayTaoBaoCao']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteBaocao($id)
    {
        $sql = "DELETE FROM baocaotaichinh WHERE BaoCaoID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // =============================
    // FILTER
    // =============================

    public function getBaocaoWithFilter($fromDate = null, $toDate = null, $maLichKhoiHanh = null)
    {
$sql = "SELECT * FROM baocaotaichinh WHERE 1=1";
        $params = [];

        if ($fromDate) {
            $sql .= " AND NgayTaoBaoCao >= :fromDate";
            $params[':fromDate'] = $fromDate;
        }

        if ($toDate) {
            $sql .= " AND NgayTaoBaoCao <= :toDate";
            $params[':toDate'] = $toDate;
        }

        if ($maLichKhoiHanh) {
            $sql .= " AND MaLichKhoiHanh = :MaLichKhoiHanh";
            $params[':MaLichKhoiHanh'] = $maLichKhoiHanh;
        }

        $sql .= " ORDER BY NgayTaoBaoCao DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================
    // SUMMARY
    // =============================

    public function getSummary($fromDate = null, $toDate = null, $maLichKhoiHanh = null)
    {
        $sql = "SELECT
                    COALESCE(SUM(DoanhThu), 0) AS total_revenue,
                    COALESCE(SUM(ChiPhi), 0)   AS total_cost,
                    COALESCE(SUM(LoiNhuan), 0) AS total_profit,
                    COALESCE(SUM(CASE WHEN LoiNhuan > 0 THEN 1 ELSE 0 END), 0) AS profit_count,
                    COALESCE(SUM(CASE WHEN LoiNhuan < 0 THEN 1 ELSE 0 END), 0) AS loss_count
                FROM baocaotaichinh
                WHERE 1=1";

        $params = [];

        if ($fromDate) {
            $sql .= " AND NgayTaoBaoCao >= :fromDate";
            $params[':fromDate'] = $fromDate;
        }

        if ($toDate) {
            $sql .= " AND NgayTaoBaoCao <= :toDate";
            $params[':toDate'] = $toDate;
        }

        if ($maLichKhoiHanh) {
            $sql .= " AND MaLichKhoiHanh = :MaLichKhoiHanh";
            $params[':MaLichKhoiHanh'] = $maLichKhoiHanh;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =============================
    // CHART DATA (THEO LỊCH)
    // =============================

    public function getChartDataByDeparture($fromDate = null, $toDate = null, $maLichKhoiHanh = null)
    {
        $sql = "SELECT
                    MaLichKhoiHanh,
                    SUM(DoanhThu)  AS total_revenue,
                    SUM(ChiPhi)    AS total_cost,
                    SUM(LoiNhuan)  AS total_profit
                FROM baocaotaichinh
                WHERE 1=1";

        $params = [];

        if ($fromDate) {
            $sql .= " AND NgayTaoBaoCao >= :fromDate";
            $params[':fromDate'] = $fromDate;
        }

        if ($toDate) {
            $sql .= " AND NgayTaoBaoCao <= :toDate";
            $params[':toDate'] = $toDate;
        }

        if ($maLichKhoiHanh) {
            $sql .= " AND MaLichKhoiHanh = :MaLichKhoiHanh";
$params[':MaLichKhoiHanh'] = $maLichKhoiHanh;
        }

        $sql .= " GROUP BY MaLichKhoiHanh ORDER BY MaLichKhoiHanh ASC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================
    // CHART DATA (THEO NGÀY)
    // =============================

    public function getChartDataByDate($fromDate = null, $toDate = null, $maLichKhoiHanh = null)
    {
        $sql = "SELECT
                    DATE(NgayTaoBaoCao) AS report_date,
                    SUM(DoanhThu)  AS total_revenue,
                    SUM(ChiPhi)    AS total_cost,
                    SUM(LoiNhuan)  AS total_profit
                FROM baocaotaichinh
                WHERE 1=1";

        $params = [];

        if ($fromDate) {
            $sql .= " AND NgayTaoBaoCao >= :fromDate";
            $params[':fromDate'] = $fromDate;
        }

        if ($toDate) {
            $sql .= " AND NgayTaoBaoCao <= :toDate";
            $params[':toDate'] = $toDate;
        }

        if ($maLichKhoiHanh) {
            $sql .= " AND MaLichKhoiHanh = :MaLichKhoiHanh";
            $params[':MaLichKhoiHanh'] = $maLichKhoiHanh;
        }

        $sql .= " GROUP BY DATE(NgayTaoBaoCao)
                  ORDER BY DATE(NgayTaoBaoCao) ASC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>