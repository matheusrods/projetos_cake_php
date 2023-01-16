USE [RHHealth]
GO

/****** Object:  Table [dbo].[ficha_psicossocial_respostas_log]    Script Date: 04/01/2023 18:46:23 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[ficha_psicossocial_respostas_log](
	[codigo] [int] IDENTITY(1,1) NOT NULL,
	[codigo_ficha_psicossocial_resposta] int NOT NULL,
	[acao_sistema] int NOT NULL,
	[codigo_ficha_psicossocial_perguntas] [int] NOT NULL,
	[codigo_ficha_psicossocial] [int] NOT NULL,
	[resposta] [varchar](500) NULL,
	[ativo] [int] NOT NULL,
	[ordem] [int] NULL,
	[data_inclusao] [datetime] NULL,
	[codigo_usuario_inclusao] [int] NOT NULL,
	[codigo_usuario_alteracao] [int] NULL,
	[data_alteracao] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[codigo] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 90, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[codigo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[resposta] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[ativo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[ordem] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[data_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[codigo_ficha_psicossocial] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[ficha_psicossocial_respostas_log].[codigo_ficha_psicossocial_perguntas] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ALTER TABLE [dbo].[ficha_psicossocial_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_ficha_psicossocial_respostas_log_codigo_ficha_psicossocial] FOREIGN KEY([codigo_ficha_psicossocial])
REFERENCES [dbo].[ficha_psicossocial] ([codigo])
GO

ALTER TABLE [dbo].[ficha_psicossocial_respostas_log] CHECK CONSTRAINT [fk_ficha_psicossocial_respostas_log_codigo_ficha_psicossocial]
GO

ALTER TABLE [dbo].[ficha_psicossocial_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_ficha_psicossocial_respostas_log_codigo_ficha_psicossocial_perguntas] FOREIGN KEY([codigo_ficha_psicossocial_perguntas])
REFERENCES [dbo].[ficha_psicossocial_perguntas] ([codigo])
GO

ALTER TABLE [dbo].[ficha_psicossocial_respostas_log] CHECK CONSTRAINT [fk_ficha_psicossocial_respostas_log_codigo_ficha_psicossocial_perguntas]
GO

ALTER TABLE [dbo].[ficha_psicossocial_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_ficha_psicossocial_respostas_log_codigo_ficha_psicossocial_resposta] FOREIGN KEY([codigo_ficha_psicossocial_resposta])
REFERENCES [dbo].[ficha_psicossocial_respostas] ([codigo])
GO
